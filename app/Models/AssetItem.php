<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'serial_number',
        'qr_code',
        'status',
    ];

    // ပစ္စည်းအမျိုးအစား (Asset) တစ်ခုနဲ့ ချိတ်ဆက်ခြင်း
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // ပြင်ဆင်ထိန်းသိမ်းမှု မှတ်တမ်းများ (Maintenance) နဲ့ ချိတ်ဆက်ခြင်း
    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class);
    }
}
