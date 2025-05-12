<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'table_id',
        'employee_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
        'notes',
        'order_type',
        'delivery_address',
    ];

    protected $casts = [
        'subtotal' => 'float',
        'tax' => 'float',
        'discount' => 'float',
        'total' => 'float'
    ];

    // Relación con el usuario (cliente)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con la mesa
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    // Relación con el empleado
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // Relación con los items de la orden
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Método para obtener el estado en español
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'pending' => 'Pendiente',
            'preparing' => 'En preparación',
            'ready' => 'Listo',
            'delivered' => 'Entregado',
            'completed' => 'Completado',
            'cancelled' => 'Cancelado'
        ];

        return $statusMap[$this->status] ?? 'Desconocido';
    }

    // Método para obtener el estado de pago en español
    public function getPaymentStatusTextAttribute()
    {
        $paymentStatusMap = [
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'refunded' => 'Reembolsado'
        ];

        return $paymentStatusMap[$this->payment_status] ?? 'Desconocido';
    }

    // Método para obtener el tipo de orden en español
    public function getOrderTypeTextAttribute()
    {
        $orderTypeMap = [
            'dine_in' => 'En restaurante',
            'takeaway' => 'Para llevar',
            'delivery' => 'Entrega a domicilio',
            'online' => 'Pedido online'
        ];

        return $orderTypeMap[$this->order_type] ?? 'Desconocido';
    }
}
