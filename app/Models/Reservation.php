<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'num_guests',
        'reservation_time',
        'duration',
        'special_requests',
        'status',
    ];

    protected $casts = [
        'reservation_time' => 'datetime',
        'num_guests' => 'integer',
        'duration' => 'integer',
    ];

    /**
     * Relación con el usuario (si está registrado)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la mesa
     */
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Obtener el estado de la reservación en formato legible
     */
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmada',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'no_show' => 'No Asistió'
        ];

        return $statusMap[$this->status] ?? 'Desconocido';
    }

    /**
     * Obtener el color del estado para las clases de CSS
     */
    public function getStatusColorAttribute()
    {
        $colorMap = [
            'pending' => 'yellow',
            'confirmed' => 'green',
            'completed' => 'blue',
            'cancelled' => 'red',
            'no_show' => 'gray'
        ];

        return $colorMap[$this->status] ?? 'gray';
    }

    /**
     * Obtener la hora de finalización de la reserva
     */
    public function getEndTimeAttribute()
    {
        return $this->reservation_time->copy()->addMinutes($this->duration);
    }
}
