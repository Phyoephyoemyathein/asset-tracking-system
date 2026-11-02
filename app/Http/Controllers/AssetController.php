<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AssetItem;

class AssetController extends Controller
{
    public function index(Request $request)
{
    // Dashboard Statistics တွက်ချက်ခြင်း
    $totalAssets = Asset::count();
    // စုစုပေါင်း လက်ကျန်ရှိနေသော (Available) ပစ္စည်းအရေအတွက်ကို တွက်ချက်ခြင်း
    $totalQuantity = \App\Models\AssetItem::where('status', 'available')->count();
    $totalCategories = Category::count();
    $totalWarehouses = Warehouse::count();

    // 📊 Chart တွေအတွက် Data တွက်ချက်ခြင်း (Available items များကို အခြေခံ၍ တွက်ချက်ခြင်း)
    $categoryData = Category::with('assets')->get();
    $categoryLabels = $categoryData->pluck('name');
    $categoryQuantities = $categoryData->map(function($category) {
        return $category->assets->sum(function($asset) {
            return $asset->items()->where('status', 'available')->count();
        });
    });

    $warehouseData = Warehouse::with('assets')->get();
    $warehouseLabels = $warehouseData->pluck('name');
    $warehouseQuantities = $warehouseData->map(function($warehouse) {
        return $warehouse->assets->sum(function($asset) {
            return $asset->items()->where('status', 'available')->count();
        });
    });

    // Main Asset Query (Available နှင့် Total count များကိုပါ တစ်ပါတည်း ယူဆောင်ခြင်း)
    $query = Asset::with(['category', 'warehouse'])
        ->withCount('items')
        ->withCount(['items as available_items_count' => function ($q) {
            $q->where('status', 'available');
        }]);

    // Search by Name or SKU
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    // Filter by Category
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Filter by Warehouse
    if ($request->filled('warehouse_id')) {
        $query->where('warehouse_id', $request->warehouse_id);
    }

    // 💡 Low Stock ဖြစ်နေသော ပစ္စည်းများကို ရှာဖွေခြင်း (Available items တွေက min_stock_level ထက် နည်း သို့မဟုတ် Equal ဖြစ်နေလျှင်)
    $lowStockAssets = Asset::with('items')->get()->filter(function($asset) {
        $availableCount = $asset->items()->where('status', 'available')->count();
        return $availableCount <= $asset->min_stock_level;
    });

    $assets = $query->latest()->paginate(5)->withQueryString();
    $categories = Category::all();
    $warehouses = Warehouse::all();

    return view('assets', compact(
        'assets', 
        'categories', 
        'warehouses', 
        'totalAssets', 
        'totalQuantity', 
        'totalCategories', 
        'totalWarehouses',
        'categoryLabels',     
        'categoryQuantities',  
        'warehouseLabels',     
        'warehouseQuantities', 
        'lowStockAssets'
    ));
}

   // Asset အသစ် သိမ်းဆည်းရန်
public function store(Request $request)
{
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized action. Only Admin can register new assets.');
    }

    $validated = $request->validate([
        'category_id'     => 'required|exists:categories,id',
        'warehouse_id'    => 'required|exists:warehouses,id',
        'name'            => 'required|string|max:255',
        'sku'             => 'required|string|unique:assets,sku',
        'quantity'        => 'required|integer|min:1',
        'min_stock_level' => 'required|integer|min:0',
    ]);

    // ၁။ Main Asset (Master Record) ကို အရင်ဆောက်မည်
    $asset = Asset::create($validated);

    // ၂။ Main Asset အတွက် Master QR Code ဖန်တီးခြင်း
    $qrCode = QrCode::size(150)->generate($asset->sku);
    $asset->update([
        'qr_code' => $qrCode
    ]);

    // ၃။ Quantity အရေအတွက်အတိုင်း asset_items ထဲသို့ Serial / QR Code သီးသန့်စီ Auto ဖန်တီးမည်
    for ($i = 1; $i <= $request->quantity; $i++) {
        $uniqueSuffix = str_pad($i, 3, '0', STR_PAD_LEFT); // 001, 002, 003...
        $serialNumber = $asset->sku . '-' . $uniqueSuffix; // ဥပမာ- SP-001-2026-001
        
        // Item တစ်ခုချင်းစီ၏ QR Code (Text / SVG)
        $itemQrCode = QrCode::size(150)->generate($serialNumber);

        AssetItem::create([
            'asset_id'      => $asset->id,
            'serial_number' => $serialNumber,
            'qr_code'       => $itemQrCode,
            'status'        => 'available',
        ]);
    }

    // ၄။ Activity Log မှတ်တမ်းတင်ခြင်း
    ActivityLog::create([
        'user_id'     => auth()->id(),
        'action'      => 'Created',
        'subject'     => 'Asset',
        'description' => auth()->user()->name . ' registered a new asset: ' . $asset->name . ' (SKU: ' . $asset->sku . ') with ' . $request->quantity . ' individual item(s).'
    ]);

    return back()->with('success', 'Asset and individual item serials/QR codes generated successfully!');
}

    // Category အသစ် သိမ်းဆည်းရန်
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name
        ]);

        return back()->with('success', 'Category added successfully!');
    }

    // Warehouse အသစ် သိမ်းဆည်းရန်
    public function storeWarehouse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name',
        ]);

        Warehouse::create([
            'name' => $request->name
        ]);

        return back()->with('success', 'Warehouse added successfully!');
    }

public function show(Asset $asset)
{
    // Category, Warehouse နဲ့ တစ်လုံးချင်းစီခွဲထားသော items များကိုပါ တစ်ခါတည်း ယူရန်
    $asset->load(['category', 'warehouse', 'items.maintenances']);
    
    // Status 'Available' ဖြစ်သော item အရေအတွက်ကို တွက်ချက်ခြင်း (သို့မဟုတ် quantity)
    // အကယ်၍ item တစ်လုံးချင်းစီ (Serial level) ဖြင့် Tracking လုပ်ထားလျှင်:
    $asset->available_total_quantity = $asset->items()->where('status', 'Available')->count();
    
    // Warehouse စာရင်းများကို ယူဆောင်လာခြင်း
    $warehouses = Warehouse::all();

    // Transaction တွေကို User ရဲ့ Role ပေါ်မူတည်၍ Filter လုပ်ခြင်း
    $transactionsQuery = $asset->transactions()->with('user');

    if (auth()->user()->role !== 'admin') {  
        $transactionsQuery->where('user_id', auth()->id());
    }

    $transactions = $transactionsQuery->latest()->get();

    return view('show', compact('asset', 'warehouses', 'transactions'));
}


// Asset အချက်အလက် ပြင်ဆင်ရန် Form သို့ ယူသွားခြင်း
    public function edit(Asset $asset)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Only Admin can edit assets.');
        }

        $categories = Category::all();
        $warehouses = Warehouse::all();
        $asset->load(['category', 'warehouse']);

        return view('edit', compact('asset', 'categories', 'warehouses'));
    }


    // Asset အချက်အလက်အသစ်များကို သိမ်းဆည်းရန် (Update)
    public function update(Request $request, Asset $asset)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Only Admin can update assets.');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:assets,sku,' . $asset->id,
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
        ]);

        // ပြောင်းလဲမှုမရှိခင် နာမည်ကို သိမ်းထားခြင်း
        $oldName = $asset->name;
        
        $asset->update($validated);

        // **Asset ပြင်ဆင်ခြင်း Log မှတ်တမ်းတင်ရန် (နာမည်ပြောင်းလဲမှုကိုပါ ဖော်ပြမည်)**
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated',
            'subject' => 'Asset',
            'description' => auth()->user()->name . ' updated asset from "' . $oldName . '" to "' . $asset->name . '" (SKU: ' . $asset->sku . ')'
        ]);

        return redirect()->to('/dashboard')->with('success', 'Asset updated successfully!');
    }


    // Asset ဖျက်ဆီးခြင်း (Destroy)
    public function destroy(Asset $asset)
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action. Only Admin can delete assets.');
        }

        $assetName = $asset->name;
        $assetSku = $asset->sku;
        
        $asset->delete();

        // **Asset ဖျက်ဆီးခြင်း Log မှတ်တမ်းတင်ရန်**
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'subject' => 'Asset',
            'description' => auth()->user()->name . ' deleted the asset: ' . $assetName . ' (SKU: ' . $assetSku . ')'
        ]);

        return redirect()->to('/dashboard')->with('success', 'Asset deleted successfully!');
    }


public function export(Request $request)
{
    $fileName = 'assets_export_' . date('Y-m-d') . '.csv';

    // Query တည်ဆောက်ခြင်း (Index မှာသုံးတဲ့ Filter တိုင်းကို ထည့်ရန်)
    $query = Asset::with(['category', 'warehouse']);

    // Search by Name or SKU
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    // Filter by Category
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Filter by Warehouse
    if ($request->filled('warehouse_id')) {
        $query->where('warehouse_id', $request->warehouse_id);
    }

    $assets = $query->latest()->get();

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $callback = function() use($assets) {
        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM ထည့်သွင်းခြင်း
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // CSV Header (ID အစား 'No' ဟု ပြောင်းထားသည်)
        fputcsv($file, ['No', 'Asset Name', 'SKU', 'Category', 'Warehouse', 'Quantity', 'Registered Date']);

        // နံပါတ်စဉ် (1, 2, 3...) ထည့်ရန် $index ကို သုံးခြင်း
        foreach ($assets as $index => $asset) {
            fputcsv($file, [
                $index + 1, // ၁, ၂, ၃ စသည်ဖြင့် အစဉ်လိုက်ပေါ်စေရန် ($index စတင်သည်မှာ 0 ဖြစ်၍ +1 လုပ်ပေးသည်)
                $asset->name,
                $asset->sku,
                $asset->category->name ?? 'N/A',
                $asset->warehouse->name ?? 'N/A',
                $asset->quantity,
                $asset->created_at
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}





// SKU ဖြင့် Scan ဖတ်လိုက်သည့်အခါ
public function scan(Request $request)
{
    $request->validate([
        'sku' => 'required|string',
    ]);

    $assets = Asset::where('sku', $request->sku)->get();

    if ($assets->isEmpty()) {
        return back()->with('error', 'Asset not found with SKU: ' . $request->sku);
    }

    // SKU တူတာနဲ့ သက်ဆိုင်တဲ့ Warehouse အားလုံးကို ပြမယ့် Page ဆီ တိုက်ရိုက် ပို့မည်
    return redirect()->route('assets.by-sku', $request->sku);
}

public function bySku($sku)
{
    // SKU တူပြီး Warehouse အလိုက် ခွဲခြားကာ Available items အရေအတွက်ကို တိကျစွာ ရေတွက်ခြင်း
    $assets = Asset::where('sku', $sku)
                ->with(['warehouse', 'category', 'items'])
                ->get()
                ->groupBy('warehouse_id')
                ->map(function ($group) {
                    $firstItem = $group->first();
                    
                    // Warehouse တစ်ခုချင်းစီအောက်တွင် Available ဖြစ်သော items အရေအတွက် (သို့မဟုတ် quantity) ကို စုစုပေါင်းပေါင်းခြင်း
                    $firstItem->total_quantity = $group->sum(function ($asset) {
                        return $asset->items()->where('status', 'Available')->count(); 
                        // (တကယ်လို့ items table မသုံးဘဲ quantity ကော်လံကိုပဲ သုံးတာဆိုရင် $asset->quantity လို့ သုံးနိုင်ပါတယ်)
                    });
                    
                    return $firstItem;
                });

    if ($assets->isEmpty()) {
        return redirect()->route('dashboard')->with('error', 'Asset not found with SKU: ' . $sku);
    }

    if ($assets->count() === 1) {
        return redirect()->route('assets.show', $assets->first()->id);
    }

    return view('sku-show', compact('assets', 'sku'));
}

public function activityLogs()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $logs = ActivityLog::with('user')->latest()->paginate(10)->withQueryString();
        
        return view('activity-logs', compact('logs'));
    }

}
