<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetItem;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
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
            'completed_date' => in_array($request->status, ['completed', 'failed']) ? now() : null,
        ]);

        // 💡 ပစ္စည်း၏ status ကို ချိန်ညှိခြင်း (Completed သို့မဟုတ် Failed ဖြစ်လျှင် available သို့ ပြန်ပြောင်းသည်)
        $item = AssetItem::findOrFail($assetId);
        if (in_array($request->status, ['completed', 'failed'])) {
            $item->update(['status' => 'available']);
        } else {
            $item->update(['status' => 'maintenance']);
        }

        return redirect()->back()->with('success', 'Maintenance record added successfully.');
    }

    // Update method တွင်လည်း ထပ်တူပြင်ရန်
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
            'completed_date' => in_array($request->status, ['completed', 'failed']) ? now() : null,
        ]);

        // 💡 Status အလိုက် AssetItem ကို ပြောင်းလဲရန်
        if (in_array($request->status, ['completed', 'failed'])) {
            $maintenance->assetItem()->update(['status' => 'available']);
        } else {
            $maintenance->assetItem()->update(['status' => 'maintenance']);
        }

        return redirect()->back()->with('success', 'Maintenance record updated successfully.');
    }
}