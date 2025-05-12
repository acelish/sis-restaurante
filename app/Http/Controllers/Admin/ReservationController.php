<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReservationConfirmed;
use App\Notifications\ReservationCancelled;

class ReservationController extends Controller
{
    /**
     * Mostrar lista de reservaciones
     */
    public function index(Request $request)
    {
        $query = Reservation::query()->with(['table', 'user']);

        // Filtros
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date') && $request->date !== '') {
            $date = Carbon::parse($request->date);
            $query->whereDate('reservation_time', $date);
        }

        if ($request->has('table_id') && $request->table_id !== '') {
            $query->where('table_id', $request->table_id);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Ordenar por fecha de reserva por defecto
        $query->orderBy('reservation_time');

        $reservations = $query->paginate(15);
        $tables = Table::orderBy('number')->get();

        return view('admin.reservations.index', compact('reservations', 'tables'));
    }

    /**
     * Mostrar el formulario para crear una nueva reserva
     */
    public function create(Request $request)
    {
        $tables = Table::where('status', '!=', 'maintenance')->orderBy('number')->get();
        $users = User::orderBy('name')->get();

        // Si se proporciona un table_id, prellenamos la mesa seleccionada
        $selectedTableId = $request->table_id;
        $selectedDate = $request->date ?? now()->format('Y-m-d');
        $selectedTime = $request->time ?? now()->format('H:i');

        return view('admin.reservations.create', compact('tables', 'users', 'selectedTableId', 'selectedDate', 'selectedTime'));
    }

    /**
     * Almacenar una nueva reserva
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'num_guests' => 'required|integer|min:1',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'duration' => 'required|integer|min:30|max:240',
            'special_requests' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,cancelled,completed,no_show',
        ]);

        // Combinar fecha y hora
        $reservationDateTime = Carbon::parse(
            $validated['reservation_date'] . ' ' . $validated['reservation_time']
        );
        
        // Verificar disponibilidad de la mesa
        $table = Table::findOrFail($validated['table_id']);
        if (!$table->isAvailableAt($reservationDateTime, $validated['duration'])) {
            return back()->withInput()->withErrors([
                'table_id' => 'La mesa seleccionada no está disponible para el horario solicitado.'
            ]);
        }

        // Crear la reserva
        $reservation = new Reservation();
        $reservation->table_id = $validated['table_id'];
        $reservation->user_id = $validated['user_id'] ?? null;
        $reservation->customer_name = $validated['customer_name'];
        $reservation->customer_email = $validated['customer_email'] ?? null;
        $reservation->customer_phone = $validated['customer_phone'] ?? null;
        $reservation->num_guests = $validated['num_guests'];
        $reservation->reservation_time = $reservationDateTime;
        $reservation->duration = $validated['duration'];
        $reservation->special_requests = $validated['special_requests'] ?? null;
        $reservation->status = $validated['status'];
        $reservation->save();

        // Si la reserva está confirmada, cambia el estado de la mesa a 'reserved'
        if ($validated['status'] === 'confirmed') {
            $table->update(['status' => 'reserved']);
        }

        // Enviar notificación si hay email
        if ($reservation->status === 'confirmed' && $reservation->customer_email) {
            try {
                Notification::route('mail', $reservation->customer_email)
                    ->notify(new ReservationConfirmed($reservation));
            } catch (\Exception $e) {
                // Continuar incluso si falla el envío del correo
                report($e);
            }
        }

        return redirect()->route('reservations.index')
            ->with('success', 'Reserva creada exitosamente');
    }

    /**
     * Mostrar detalles de una reserva
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['table', 'user']);
        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Mostrar formulario para editar una reserva
     */
    public function edit(Reservation $reservation)
    {
        $tables = Table::where('status', '!=', 'maintenance')->orderBy('number')->get();
        $users = User::orderBy('name')->get();
        
        return view('admin.reservations.edit', compact('reservation', 'tables', 'users'));
    }

    /**
     * Actualizar una reserva
     */
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'table_id' => 'required|exists:tables,id',
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'num_guests' => 'required|integer|min:1',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'duration' => 'required|integer|min:30|max:240',
            'special_requests' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,cancelled,completed,no_show',
        ]);

        // Combinar fecha y hora
        $reservationDateTime = Carbon::parse(
            $validated['reservation_date'] . ' ' . $validated['reservation_time']
        );
        
        // Verificar disponibilidad solo si cambia la mesa, fecha u hora
        $tableChanged = $reservation->table_id != $validated['table_id'];
        $timeChanged = $reservation->reservation_time->format('Y-m-d H:i') !== $reservationDateTime->format('Y-m-d H:i');
        $durationChanged = $reservation->duration != $validated['duration'];
        
        if (($tableChanged || $timeChanged || $durationChanged) && $validated['status'] !== 'cancelled') {
            $table = Table::findOrFail($validated['table_id']);
            
            // Excluimos la reserva actual de la verificación de disponibilidad
            $isAvailable = !$table->reservations()
                ->where('id', '!=', $reservation->id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($reservationDateTime, $validated) {
                    $endTime = (clone $reservationDateTime)->addMinutes($validated['duration']);
                    
                    $query->where(function ($q) use ($reservationDateTime, $endTime) {
                        $q->where('reservation_time', '<', $endTime->format('Y-m-d H:i:s'))
                            ->whereRaw('DATE_ADD(reservation_time, INTERVAL duration MINUTE) > ?', [
                                $reservationDateTime->format('Y-m-d H:i:s')
                            ]);
                    });
                })
                ->exists();
                
            if (!$isAvailable) {
                return back()->withInput()->withErrors([
                    'table_id' => 'La mesa seleccionada no está disponible para el horario solicitado.'
                ]);
            }
        }

        // Guardar estado anterior para comparación
        $oldStatus = $reservation->status;
        
        // Actualizar la reserva
        $reservation->table_id = $validated['table_id'];
        $reservation->user_id = $validated['user_id'] ?? null;
        $reservation->customer_name = $validated['customer_name'];
        $reservation->customer_email = $validated['customer_email'] ?? null;
        $reservation->customer_phone = $validated['customer_phone'] ?? null;
        $reservation->num_guests = $validated['num_guests'];
        $reservation->reservation_time = $reservationDateTime;
        $reservation->duration = $validated['duration'];
        $reservation->special_requests = $validated['special_requests'] ?? null;
        $reservation->status = $validated['status'];
        $reservation->save();

        // Enviar notificaciones si hay cambios de estado
        if ($reservation->customer_email) {
            try {
                if ($oldStatus !== 'confirmed' && $reservation->status === 'confirmed') {
                    Notification::route('mail', $reservation->customer_email)
                        ->notify(new ReservationConfirmed($reservation));
                } elseif ($oldStatus !== 'cancelled' && $reservation->status === 'cancelled') {
                    Notification::route('mail', $reservation->customer_email)
                        ->notify(new ReservationCancelled($reservation));
                }
            } catch (\Exception $e) {
                // Continuar incluso si falla el envío del correo
                report($e);
            }
        }

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reserva actualizada exitosamente');
    }

    /**
     * Eliminar una reserva
     */
    public function destroy(Reservation $reservation)
    {
        // Solo permitir eliminar reservas canceladas o completadas
        if (!in_array($reservation->status, ['cancelled', 'completed', 'no_show'])) {
            return back()->with('error', 'Solo se pueden eliminar reservas canceladas, completadas o marcadas como no asistidas');
        }

        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reserva eliminada exitosamente');
    }

    /**
     * Actualizar rápidamente el estado de una reserva
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed,no_show',
        ]);

        $oldStatus = $reservation->status;
        $reservation->status = $validated['status'];
        $reservation->save();

        // Enviar notificaciones si hay cambios de estado
        if ($reservation->customer_email) {
            try {
                if ($oldStatus !== 'confirmed' && $reservation->status === 'confirmed') {
                    Notification::route('mail', $reservation->customer_email)
                        ->notify(new ReservationConfirmed($reservation));
                } elseif ($oldStatus !== 'cancelled' && $reservation->status === 'cancelled') {
                    Notification::route('mail', $reservation->customer_email)
                        ->notify(new ReservationCancelled($reservation));
                }
            } catch (\Exception $e) {
                // Continuar incluso si falla el envío del correo
                report($e);
            }
        }

        return back()->with('success', 'Estado de la reserva actualizado a ' . $reservation->status_text);
    }

    /**
     * Mostrar calendario de reservas
     */
    public function calendar(Request $request)
    {
        $tables = Table::where('status', '!=', 'maintenance')->orderBy('number')->get();
        $selectedDate = $request->date ?? now()->format('Y-m-d');
        
        $reservations = Reservation::with('table')
            ->where('status', '!=', 'cancelled')
            ->whereBetween('reservation_time', [
                Carbon::parse($selectedDate)->startOfDay(),
                Carbon::parse($selectedDate)->endOfDay(),
            ])
            ->orderBy('reservation_time')
            ->get();
            
        return view('admin.reservations.calendar', compact('tables', 'reservations', 'selectedDate'));
    }

    /**
     * Verificar disponibilidad de mesas
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer|min:30|max:240',
            'num_guests' => 'required|integer|min:1',
            'table_id' => 'nullable|exists:tables,id',
        ]);

        $reservationDateTime = Carbon::parse($request->date . ' ' . $request->time);
        $duration = $request->duration;
        $numGuests = $request->num_guests;
        
        // Buscar mesas disponibles
        $tables = Table::where('capacity', '>=', $numGuests)
            ->where('status', '!=', 'maintenance')
            ->get()
            ->filter(function($table) use ($reservationDateTime, $duration) {
                return $table->isAvailableAt($reservationDateTime, $duration);
            })
            ->values();
            
        // Verificar si la mesa específica está disponible
        $isSpecificTableAvailable = false;
        if ($request->table_id) {
            $specificTable = Table::find($request->table_id);
            $isSpecificTableAvailable = $specificTable && $specificTable->isAvailableAt($reservationDateTime, $duration);
        }
        
        return response()->json([
            'available_tables' => $tables,
            'is_specific_table_available' => $isSpecificTableAvailable,
        ]);
    }
}
