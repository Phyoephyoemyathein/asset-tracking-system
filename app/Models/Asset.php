<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'warehouse_id',
        'name',
        'sku',
        'qr_code',
        'quantity',
        'min_stock_level',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function transactions(): HasMany
{
    return $this->hasMany(StockTransaction::class);
}

public function isLowStock()
{
    return $this->quantity <= $this->min_stock_level;
}

public function transfers()
{
    return $this->hasMany(AssetTransfer::class);
}

public function maintenances()
{
    return $this->hasMany(AssetMaintenance::class);
}

public function items()
{
    return $this->hasMany(AssetItem::class);
}

}
