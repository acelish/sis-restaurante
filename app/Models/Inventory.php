// Modelo Inventory.php
public function movements()
{
    return $this->hasMany(InventoryMovement::class);
}

// Controlador para alertas de stock bajo
public function checkLowStock()
{
    $alerts = Inventory::where('current_stock', '<', 'minimum_stock')->get();
    // Enviar notificación a encargados
}