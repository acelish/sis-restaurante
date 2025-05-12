<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReservationConfirmed;

class ReservationController extends Controller
{
    /**
     * Mostrar la página de reservaciones
     */
    public function index()
    {
        // Obtener datos necesarios para el formulario
        $today = Carbon::today()->format('Y-m-d');
        $maxDate = Carbon::today()->addMonths(2)->format('Y-m-d');
        $availableTimes = $this->getAvailableReservationTimes();
        
        return view('client.reservation.index', compact('today', 'maxDate', 'availableTimes'));
    }
    
    /**
     * Verificar disponibilidad de mesas
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'guests' => 'required|integer|min:1|max:20',
        ]);

        $date = $request->date;
        $time = $request->time;
        $guests = $request->guests;
        
        $reservationDateTime = Carbon::parse($date . ' ' . $time);
        
        // Solo permitir reservas con al menos 2 horas de anticipación
        if ($reservationDateTime->isPast() || $reservationDateTime->diffInHours(Carbon::now()) < 2) {
            return response()->json([
                'available' => false,
                'message' => 'Las reservaciones deben hacerse con al menos 2 horas de anticipación.'
            ]);
        }
        
        // Por defecto, duraciones de 90 minutos para reservas de clientes
        $duration = 90;
        
        // Buscar mesas disponibles
        $tables = Table::where('capacity', '>=', $guests)
            ->where('status', '!=', 'maintenance')
            ->orderBy('capacity')
            ->get()
            ->filter(function($table) use ($reservationDateTime, $duration) {
                return $table->isAvailableAt($reservationDateTime, $duration);
            })
            ->values();
        
        return response()->json([
            'available' => $tables->count() > 0,
            'tables' => $tables,
            'date' => $reservationDateTime->format('d/m/Y'),
            'time' => $reservationDateTime->format('H:i'),
            'message' => $tables->count() > 0 
                ? 'Hay ' . $tables->count() . ' mesas disponibles para tu reservación.' 
                : 'Lo sentimos, no hay mesas disponibles para el horario seleccionado.'
        ]);
    }
    
    /**
     * Almacenar una nueva reserva o redirigir a WhatsApp
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'num_guests' => 'required|integer|min:1|max:20',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'special_requests' => 'nullable|string|max:500',
        ]);

        // Combinar fecha y hora
        $reservationDateTime = Carbon::parse(
            $validated['reservation_date'] . ' ' . $validated['reservation_time']
        );
        
        // Construir mensaje para WhatsApp
        $text = "Hola, soy {$validated['customer_name']} y me gustaría hacer una reservación para {$validated['num_guests']} personas el día {$reservationDateTime->format('d/m/Y')} a las {$reservationDateTime->format('h:i A')}. ";
        
        if (!empty($validated['special_requests'])) {
            $text .= "Solicitudes especiales: {$validated['special_requests']}. ";
        }
        
        $text .= "Mi número de contacto es {$validated['customer_phone']} y mi correo es {$validated['customer_email']}. Agradecería su confirmación. ¡Gracias!";
        
        // Número de WhatsApp del restaurante (reemplazar con el número real)
        $restaurantPhone = '5215512345678';
        
        // Construir la URL de WhatsApp
        $whatsappUrl = "https://wa.me/{$restaurantPhone}?text=" . urlencode($text);
        
        // Envía mensaje de éxito y redirige a WhatsApp
        return redirect()->away($whatsappUrl);
    }
    
    /**
     * Mostrar página de confirmación de reserva (versión simplificada)
     */
    public function success(Request $request)
    {
        return view('client.reservation.success');
    }
    
    /**
     * Generar horarios disponibles para reservas
     */
    private function getAvailableReservationTimes()
    {
        $times = [];
        $start = Carbon::createFromTime(12, 0); // 12:00 PM
        $end = Carbon::createFromTime(21, 0);   // 9:00 PM
        
        $current = clone $start;
        
        while ($current <= $end) {
            $times[] = $current->format('H:i');
            $current->addMinutes(30);
        }
        
        return $times;
    }
    
    /**
     * Enviar mensaje a WhatsApp
     */
    public function contactWhatsApp(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'message' => 'nullable|string',
        ]);
        
        // Número de WhatsApp del restaurante (reemplazar con el número real)
        $restaurantPhone = '5215512345678';
        
        // Construir el mensaje
        $text = "Hola, soy {$request->name} y me gustaría hacer una reservación. ";
        
        if (!empty($request->message)) {
            $text .= $request->message;
        } else {
            $text .= "¿Podrían ayudarme con información sobre disponibilidad?";
        }
        
        // Construir la URL de WhatsApp
        $whatsappUrl = "https://wa.me/{$restaurantPhone}?text=" . urlencode($text);
        
        return response()->json([
            'url' => $whatsappUrl
        ]);
    }
}
