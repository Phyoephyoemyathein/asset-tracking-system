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
        'assigned_to',
    ];

 
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

 
    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function assignee()
{
    return $this->belongsTo(\App\Models\User::class, 'assigned_to');
}

}
