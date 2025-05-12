<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'quantity',
        'alert_threshold',
        'cost'
    ];

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->alert_threshold;
    }
}
