<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetItem;
use App\Models\AssetTransfer;
use App\Models\Warehouse;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetTransferController extends Controller
{
    public function store(Request $request, Asset $asset)
    {
        // Admin မဟုတ်လျှင် ခွင့်မပြုပါ
    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized action. Only administrators can perform warehouse transfers.');
    }

        $request->validate([
            'to_warehouse_id' => 'required|exists:warehouses,id|different:' . $asset->warehouse_id,
            'serial_ids' => 'required|array',
            'serial_ids.*' => 'exists:asset_items,id',
            'reference' => 'nullable|string|max:255',
        ], [
            'to_warehouse_id.different' => 'မူလ Warehouse နဲ့ မတူတဲ့ တခြား Warehouse ကို ရွေးချယ်ပေးပါ၊၊',
            'serial_ids.required' => 'ကျေးဇူးပြု၍ လွှဲပြောင်းမည့် Serial များကို ရွေးချယ်ပါ။',
        ]);

        DB::transaction(function () use ($request, $asset) {
            $fromWarehouseId = $asset->warehouse_id;
            $toWarehouseId = $request->to_warehouse_id;
            $serialIds = $request->serial_ids;

            // ရွေးချယ်ထားသော Available ဖြစ်နေသော Serial များကို ယူရန်
            $serials = AssetItem::whereIn('id', $serialIds)
                ->where('asset_id', $asset->id)
                ->where('status', 'available')
                ->get();

            if ($serials->isEmpty()) {
                throw new \Exception('လွှဲပြောင်းရန် ရွေးချယ်ထားသော Serial များ မမှန်ကန်ပါ။');
            }

            $transferQty = $serials->count();
            $serialNumbers = $serials->pluck('serial_number')->toArray();

            // ၁။ Destination Warehouse တွင် ဤ SKU ဖြင့် Asset ရှိပြီးသားလား စစ်ဆေးခြင်း
            $destinationAsset = Asset::where('warehouse_id', $toWarehouseId)
                ->where('sku', $asset->sku)
                ->first();

            if (!$destinationAsset) {
                // မရှိသေးပါက Warehouse အသစ်အတွက် Asset အသစ် ဖန်တီးမည်
                $destinationAsset = Asset::create([
                    'category_id' => $asset->category_id,
                    'warehouse_id' => $toWarehouseId,
                    'name' => $asset->name,
                    'sku' => $asset->sku, 
                    'qr_code' => $asset->qr_code,
                    'min_stock_level' => $asset->min_stock_level,
                ]);
            }

            // ၂။ ရွေးချယ်ထားသော Serial များကို Destination Asset နှင့် Warehouse သို့ ပြောင်းလဲခြင်း
            AssetItem::whereIn('id', $serialIds)->update([
                'asset_id' => $destinationAsset->id,
                'warehouse_id' => $toWarehouseId,
            ]);

            // ၃။ Transfer မှတ်တမ်း သိမ်းဆည်းခြင်း
            AssetTransfer::create([
                'asset_id' => $asset->id,
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id' => $toWarehouseId,
                'quantity' => $transferQty,
                'reference' => $request->reference . ' (Transferred Serials: ' . implode(', ', $serialNumbers) . ')',
                'user_id' => Auth::id(),
            ]);

            // ၄။ Stock Transaction History တွင်လည်း မှတ်တမ်းတင်ရန် (optional)
            StockTransaction::create([
                'asset_id' => $asset->id,
                'type' => 'Transfer',
                'quantity' => $transferQty,
                'reference' => 'Transferred to Warehouse ID: ' . $toWarehouseId . ' (Serials: ' . implode(', ', $serialNumbers) . ') ' . $request->reference,
                'user_id' => Auth::id(),
            ]);
        });

        return back()->with('transfer_success', 'The asset has been successfully transferred to another warehouse with selected serial numbers.');
    }
}