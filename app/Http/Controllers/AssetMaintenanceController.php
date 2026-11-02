<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\AssetItem;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
{
    // ပစ္စည်းတစ်ခုချင်းစီ၏ Maintenance မှတ်တမ်းများကို ပြသရန်
   public function index($id)
{
    $asset = AssetItem::with('asset', 'maintenances')->findOrFail($id);
    return view('maintenance.index', compact('asset'));
}

    // Maintenance မှတ်တမ်းအသစ် သိမ်းဆည်းရန်
    public function store(Request $request, $assetId)
    {
        $request->validate([
            'issue_description' => 'required|string|max:255',
            'action_text' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'reported_date' => 'required|date',
            'status' => 'required|string',
        ]);

        AssetMaintenance::create([
            'asset_item_id' => $assetId,
            'issue_description' => $request->issue_description,
            'action_taken' => $request->action_text,
            'cost' => $request->cost ?? 0,
            'status' => $request->status,
            'reported_date' => $request->reported_date,
            'completed_date' => $request->status == 'completed' ? now() : null,
        ]);

        // ပစ္စည်းတစ်ခုချင်းစီ၏ Status ကို Maintenance အခြေအနေအလိုက် အလိုအလျောက် ပြောင်းလဲရန်
        $item = AssetItem::findOrFail($assetId);
        if ($request->status == 'completed') {
            $item->update(['status' => 'available']);
        } else {
            $item->update(['status' => 'maintenance']);
        }

        return redirect()->back()->with('success', 'Maintenance record added successfully.');
    }

    // Maintenance မှတ်တမ်း ဖျက်ရန်
    public function destroy($id)
    {
        $maintenance = AssetMaintenance::findOrFail($id);
        $maintenance->delete();

        return redirect()->back()->with('success', 'Maintenance record deleted successfully.');
    }


public function update(Request $request, $id)
{
    $request->validate([
        'status' => 'required|string',
        'action_text' => 'nullable|string',
    ]);

    $maintenance = AssetMaintenance::findOrFail($id);

    $maintenance->update([
        'status' => $request->status,
        'action_taken' => $request->action_text,
        'completed_date' => $request->status == 'completed' ? now() : null,
    ]);

   // ပစ္စည်း၏ status ကိုပါ အလိုအလျောက် ချိန်ညှိရန်
    if ($request->status == 'completed') {
        $maintenance->assetItem()->update(['status' => 'available']);
    } elseif ($request->status == 'failed') {
        $maintenance->assetItem()->update(['status' => 'failed']); // 💡 $item အစား $maintenance->assetItem() ကို သုံးရန်
    } else {
        $maintenance->assetItem()->update(['status' => 'maintenance']);
    }

    return redirect()->back()->with('success', 'Maintenance record updated successfully.');
}
}