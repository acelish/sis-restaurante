<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'capacity',
        'status'
    ];

    /**
     * Obtener el estado de la mesa en formato legible
     */
    public function getStatusTextAttribute()
    {
        $statusMap = [
            'available' => 'Disponible',
            'occupied' => 'Ocupada',
            'reserved' => 'Reservada',
            'maintenance' => 'En mantenimiento'
        ];

        return $statusMap[$this->status] ?? 'Desconocido';
    }

    /**
     * Obtener el color de la clase según el estado
     */
    public function getStatusColorAttribute()
    {
        $colorMap = [
            'available' => 'green',
            'occupied' => 'red',
            'reserved' => 'blue',
            'maintenance' => 'gray'
        ];

        return $colorMap[$this->status] ?? 'gray';
    }

    /**
     * Relación con órdenes
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relación con reservas
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Verificar si la mesa está disponible en una fecha y hora específica
     */
    public function isAvailableAt($dateTime, $duration = 90)
    {
        // Si la mesa está en mantenimiento, no está disponible
        if ($this->status === 'maintenance') {
            return false;
        }
        
        // Duración en minutos (por defecto 90 min)
        $startTime = $dateTime->copy();
        $endTime = $dateTime->copy()->addMinutes($duration);
        
        // Verificar si hay reservaciones que se solapan con el horario solicitado
        $conflictingReservations = $this->reservations()
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                // Reservaciones que empiezan durante nuestra reservación
                $query->whereBetween('reservation_time', [$startTime, $endTime])
                    // O que terminan durante nuestra reservación
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->where('reservation_time', '<=', $startTime)
                            ->whereRaw('DATE_ADD(reservation_time, INTERVAL duration MINUTE) >= ?', [$startTime]);
                    });
            })
            ->count();
        
        return $conflictingReservations === 0;
    }
}
