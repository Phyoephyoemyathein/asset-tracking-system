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
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowStockNotification;
use Illuminate\Support\Facades\DB;

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

        // 📅 Date Range Filter (စတင်ရက်စွဲ နှင့် ပြီးဆုံးရက်စွဲ အလိုက် စစ်ထုတ်ခြင်း)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', 
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        // 💡 Low Stock ဖြစ်နေသော ပစ္စည်းများကို ရှာဖွေခြင်း (Available items တွေက min_stock_level ထက် နည်း သို့မဟုတ် Equal ဖြစ်နေလျှင်)
        $lowStockAssets = Asset::with('items')->get()->filter(function($asset) {
            $availableCount = $asset->items()->where('status', 'available')->count();
            return $availableCount <= $asset->min_stock_level;
        });

        // 💻 Screen ပေါ်တွင် ပြရန် Pagination (5 ခုစီ)
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
            $uniqueSuffix = str_pad($i, 3, '0', STR_PAD_LEFT); 
            $serialNumber = $asset->sku . '-' . $uniqueSuffix; 
            
            $itemQrCode = QrCode::size(150)->generate($serialNumber);

            AssetItem::create([
                'asset_id'      => $asset->id,
                'serial_number' => $serialNumber,
                'qr_code'       => $itemQrCode,
                'status'        => 'available',
            ]);
        }

        // 💡 ၄။ ထည့်သွင်းလိုက်သော ပစ္စည်းသည် Low Stock အဆင့်ရောက်နေပါက Login ဝင်ထားသော Admin ၏ Email ဆီသို့ ပို့မည်
        $availableCount = $asset->items()->where('status', 'available')->count();
        if ($availableCount <= $asset->min_stock_level) {
            Mail::to(auth()->user()->email)->send(new LowStockNotification($asset));
        }

        // ၅။ Activity Log မှတ်တမ်းတင်ခြင်း
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
    $asset->load(['category', 'warehouse', 'assignee']);
    
    $asset->available_total_quantity = $asset->items()->where('status', 'Available')->count();
    
    // 💡 Database မှ Item အားလုံးကို အရင်ယူမည်
    $allItems = $asset->items()->with(['maintenances', 'assignee'])->orderBy('serial_number', 'asc')->get();
    
    // 💡 Screen ပေါ်တွင် 5 ခုစီ Page ခွဲပြရန် Collection Pagination ကို သုံးမည်
    $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
    $perPage = 5;
    $currentItems = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();
    
    $items = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentItems,
        $allItems->count(),
        $perPage,
        $currentPage,
        ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
    );
    
    $warehouses = Warehouse::all();
    $users = \App\Models\User::all();

    $transactionsQuery = $asset->transactions()->with('user');

    if (auth()->user()->role !== 'admin') {  
        $transactionsQuery->where('user_id', auth()->id());
    }

    $transactions = $transactionsQuery->latest()->get();

    return view('show', compact('asset', 'items', 'allItems', 'warehouses', 'transactions', 'users'));
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



public function update(Request $request, $id)
{
    if (!auth()->check() || !auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized action. Only Admin can update assets.');
    }

    $asset = Asset::with(['category', 'warehouse'])->findOrFail($id);

    $validated = $request->validate([
        'category_id'     => 'required|exists:categories,id',
        'warehouse_id'    => 'required|exists:warehouses,id',
        'name'            => 'required|string|max:255',
        'sku'             => [
            'required',
            'string',
            \Illuminate\Validation\Rule::unique('assets', 'sku')
                ->ignore($id)
                ->where(function ($query) use ($request) {
                    return $query->where('warehouse_id', $request->warehouse_id);
                }),
        ],
        'min_stock_level' => 'required|integer|min:0',
    ]);

    // 💡 ပြောင်းလဲမှုများကို တိုက်ဆိုင်စစ်ဆေးရန် အဟောင်းများကို သိမ်းထားမည်
    $oldName = $asset->name;
    $oldSku = $asset->sku;
    $oldMinStock = (int) $asset->min_stock_level;
    $oldCategoryId = $asset->category_id;
    $oldWarehouseId = $asset->warehouse_id;

    $asset->fill($validated);

    if ($asset->isDirty()) {
        $asset->save();
        $asset->load(['category', 'warehouse']);

        // Low Stock Email Check
        $availableCount = $asset->items()->where('status', 'available')->count();
        if ($availableCount <= $asset->min_stock_level) {
            Mail::to(auth()->user()->email)->send(new LowStockNotification($asset));
        }

        // 💡 တကယ်ပြောင်းလဲသွားသော Fields များကိုသာ တိတိကျကျ စစ်ဆေး၍ ထည့်မည်
        $changes = [];
        
        if ($oldName !== $asset->name) {
            $changes[] = 'name from "' . $oldName . '" to "' . $asset->name . '"';
        }
        if ($oldSku !== $asset->sku) {
            $changes[] = 'SKU from "' . $oldSku . '" to "' . $asset->sku . '"';
        }
        if ($oldMinStock !== (int) $asset->min_stock_level) {
            $changes[] = 'min stock level from ' . $oldMinStock . ' to ' . $asset->min_stock_level;
        }
        if ($oldCategoryId != $asset->category_id) {
            $oldCatName = \App\Models\Category::find($oldCategoryId)->name ?? 'N/A';
            $changes[] = 'category from "' . $oldCatName . '" to "' . ($asset->category->name ?? 'N/A') . '"';
        }
        if ($oldWarehouseId != $asset->warehouse_id) {
            $oldWhName = \App\Models\Warehouse::find($oldWarehouseId)->name ?? 'N/A';
            $changes[] = 'warehouse from "' . $oldWhName . '" to "' . ($asset->warehouse->name ?? 'N/A') . '"';
        }

        $descriptionText = count($changes) > 0 
            ? auth()->user()->name . ' updated asset "' . $asset->name . '": changed ' . implode(', ', $changes)
            : auth()->user()->name . ' updated asset: ' . $asset->name;

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'Updated',
            'subject'     => 'Asset',
            'description' => $descriptionText
        ]);
    }

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

        $query = Asset::with(['category', 'warehouse']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00', 
                $request->end_date . ' 23:59:59'
            ]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
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
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['No', 'Asset Name', 'SKU', 'Category', 'Warehouse', 'Quantity', 'Registered Date']);

            foreach ($assets as $index => $asset) {
                fputcsv($file, [
                    $index + 1,
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


   public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt,xlsx|max:2048',
    ]);

    $file = $request->file('file');
    $path = $file->getRealPath();
    
    $data = array_map('str_getcsv', file($path));
    $header = array_shift($data); // ပထမဆုံး row (header) ကို ဖယ်ထုတ်သည်

    DB::beginTransaction();
    try {
        foreach ($data as $row) {
            if (count($row) < 6) continue;

            $name = trim($row[0]);
            $sku = trim($row[1]);
            $categoryName = trim($row[2]);   // CSV မှ Category Name
            $warehouseName = trim($row[3]); // CSV မှ Warehouse Name
            $quantity = (int)$row[4];
            $minStockLevel = (int)$row[5];

            // 1. Category ကို နာမည်ဖြင့် ရှာမည် (မရှိလျှင် အသစ်မဆောက်ပါ)
$category = \App\Models\Category::where('name', $categoryName)->first();

// 2. Warehouse ကို နာမည်ဖြင့် ရှာမည် (မရှိလျှင် အသစ်မဆောက်ပါ)
$warehouse = \App\Models\Warehouse::where('name', $warehouseName)->first();

// အကယ်၍ Category သို့မဟုတ် Warehouse မတွေ့ပါက ဤ Row ကို ကျော်မည် (Skip)
if (!$category || !$warehouse) {
    continue; 
}

            // 💡 Asset တစ်ခုလုံးအတွက် QR Code ဂျင်နရေတာ လုပ်ခြင်း
            $assetQrSvg = QrCode::size(200)->generate($sku);

            // Asset ကို ဖန်တီးခြင်း သို့မဟုတ် ရှာဖွေခြင်း
            $asset = Asset::firstOrCreate(
                ['sku' => $sku],
                [
                    'name' => $name,
                    'category_id' => $category->id,     // 👈 Correct ID from Category Name
                    'warehouse_id' => $warehouse->id,   // 👈 Correct ID from Warehouse Name
                    'min_stock_level' => $minStockLevel,
                    'qr_code' => $assetQrSvg,
                ]
            );

            // Quantity အတိုင်း AssetItem တစ်ခုချင်းစီအတွက် Serial နှင့် QR Code ထည့်သွင်းခြင်း
            for ($i = 1; $i <= $quantity; $i++) {
                $serialNumber = $sku . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                $itemQrSvg = QrCode::size(100)->generate($serialNumber);

                AssetItem::firstOrCreate(
                    ['serial_number' => $serialNumber],
                    [
                        'asset_id' => $asset->id,
                        'status' => 'available',
                        'qr_code' => $itemQrSvg,
                    ]
                );
            }
        }

        DB::commit();
        return redirect()->back()->with('success', 'Assets and items imported successfully with QR codes!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
    }
}


public function downloadSampleCsv()
{
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="sample_assets_import.csv"',
    ];

    // ID များအစား Name များကို Header တွင် သုံးပေးခြင်း
    $columns = ['Name', 'SKU', 'Category Name', 'Warehouse Name', 'Quantity', 'Min Stock Level'];

    $callback = function() use ($columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        // ID အစား User နားလည်လွယ်မည့် နာမည် အစစ်အမှန်များဖြင့် Sample ထည့်ပေးခြင်း
        fputcsv($file, ['Dell XPS 15 Laptop', 'LAP-DELL-001', 'IT Equipments', 'Main Warehouse', '5', '2']);
        fputcsv($file, ['Logitech MX Master 3', 'MSE-LOG-002', 'General Equipments', 'IT Warehouse', '10', '3']);
        
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

        return redirect()->route('assets.by-sku', $request->sku);
    }



    public function bySku($sku)
    {
        $assets = Asset::where('sku', $sku)
                    ->with(['warehouse', 'category', 'items'])
                    ->get()
                    ->groupBy('warehouse_id')
                    ->map(function ($group) {
                        $firstItem = $group->first();
                        
                        $firstItem->total_quantity = $group->sum(function ($asset) {
                            return $asset->items()->where('status', 'Available')->count(); 
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



    public function assign(Request $request, Asset $asset)
{
    $request->validate([
        'assigned_to' => 'nullable|exists:users,id',
    ]);

    $asset->update([
        'assigned_to' => $request->assigned_to,
    ]);

    return back()->with('success', 'Asset assigned successfully!');
}


public function assignItem(Request $request, $id)
{
    if (auth()->user()->role !== 'admin') {
        abort(403, 'Unauthorized action. Admin access only.');
    }

    $item = \App\Models\AssetItem::with('assignee')->findOrFail($id);
    
    // 💡 Update မလုပ်ခင် အဟောင်းနာမည်ကို အရင်သိမ်းထားပါ
    $oldAssigneeName = $item->assignee ? $item->assignee->name : 'Unassigned';

    $newAssigneeId = $request->input('assigned_to');

    // အသစ်ရွေးလိုက်သော User ၏ နာမည်ကို ရှာရန်
    $newAssigneeName = 'Unassigned';
    if (!empty($newAssigneeId)) {
        $user = \App\Models\User::find($newAssigneeId);
        if ($user) {
            $newAssigneeName = $user->name;
        }
    }

    // 💡 အချက်အလက် တကယ်ပြောင်းလဲမှသာ Update လုပ်မည်
    if ($item->assigned_to != $newAssigneeId) {
        $item->update([
            'assigned_to' => $newAssigneeId
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'Assigned',
            'subject'     => 'Asset Item',
            'description' => auth()->user()->name . ' assigned serial item ' . $item->serial_number . ' from ' . $oldAssigneeName . ' to ' . $newAssigneeName
        ]);
    }

    return back()->with('success', 'Serial item assigned successfully!');
}


public function printAll()
{
    
    $assets = Asset::with(['category', 'warehouse'])
                ->withCount('items')
                ->withCount(['items as available_items_count' => function ($query) {
                    $query->where('status', 'available');
                }])
                ->get();

    return view('print', compact('assets'));
}



}