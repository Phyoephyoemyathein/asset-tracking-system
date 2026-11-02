<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::withCount('assets')->latest()->get();
        return view('warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:warehouses']);
        
        $warehouse = Warehouse::create($request->only('name'));

        // **Warehouse အသစ်ဆောက်လုပ်ခြင်း Log မှတ်တမ်းတင်ရန်**
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created',
            'subject' => 'Warehouse',
            'description' => auth()->user()->name . ' added a new warehouse: ' . $warehouse->name
        ]);

        return redirect()->back()->with('success', 'Warehouse created successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if($warehouse->assets()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete warehouse containing assets.');
        }

        $warehouseName = $warehouse->name;
        $warehouse->delete();

        // **Warehouse ဖျက်ဆီးခြင်း Log မှတ်တမ်းတင်ရန်**
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'subject' => 'Warehouse',
            'description' => auth()->user()->name . ' deleted the warehouse: ' . $warehouseName
        ]);

        return redirect()->back()->with('success', 'Warehouse deleted successfully.');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name,' . $warehouse->id
        ]);

        $oldName = $warehouse->name;
        $warehouse->update($request->only('name'));

        // **Warehouse အချက်အလက် ပြင်ဆင်ခြင်း Log မှတ်တမ်းတင်ရန်**
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated',
            'subject' => 'Warehouse',
            'description' => auth()->user()->name . ' updated warehouse from "' . $oldName . '" to "' . $warehouse->name . '"'
        ]);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully.');
    }
}