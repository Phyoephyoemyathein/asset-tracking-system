<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_item_id',
        'issue_description',
        'action_taken',
        'cost',
        'status',
        'reported_date',
    ];

    

    public function assetItem()
{
    return $this->belongsTo(AssetItem::class, 'asset_item_id');
}

}