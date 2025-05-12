<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'image',
        'is_available',
        'stock',
        'track_inventory',
        'cost',
    ];

    protected $casts = [
        'price' => 'float',
        'cost' => 'float',
        'is_available' => 'boolean',
        'track_inventory' => 'boolean',
        'stock' => 'integer',
    ];

    /**
     * Relación con la categoría
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con los items de inventario (ingredientes)
     */
    public function inventoryItems()
    {
        return $this->belongsToMany(InventoryItem::class, 'product_inventory')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Relación con las órdenes (a través de los items de orden)
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Verificar si el producto tiene stock suficiente
     */
    public function hasStock($quantity = 1)
    {
        if (!$this->track_inventory) {
            return true; // Si no se controla el inventario, siempre hay stock
        }
        
        return $this->stock >= $quantity;
    }

    /**
     * Verificar si todos los ingredientes tienen stock suficiente
     */
    public function hasIngredientsStock($quantity = 1)
    {
        foreach ($this->inventoryItems as $item) {
            $requiredQuantity = $item->pivot->quantity * $quantity;
            if ($item->quantity < $requiredQuantity) {
                return false;
            }
        }
        
        return true;
    }
}
