<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class ReservationConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $reservation = $this->reservation;
        $table = $reservation->table;
        
        $dateFormatted = Carbon::parse($reservation->reservation_time)->format('d/m/Y');
        $timeFormatted = Carbon::parse($reservation->reservation_time)->format('H:i');
        $endTime = Carbon::parse($reservation->reservation_time)->addMinutes($reservation->duration)->format('H:i');
        
        return (new MailMessage)
            ->subject('¡Reserva Confirmada en Restaurante!')
            ->greeting('Hola, ' . $reservation->customer_name)
            ->line('Tu reserva ha sido confirmada.')
            ->line('**Detalles de la reserva:**')
            ->line("**Fecha:** {$dateFormatted}")
            ->line("**Hora:** {$timeFormatted} - {$endTime}")
            ->line("**Mesa:** {$table->number}")
            ->line("**Personas:** {$reservation->num_guests}")
            ->line("Nos vemos pronto. ¡Gracias por elegir nuestro restaurante!")
            ->action('Ver el menú', url('/menu'))
            ->line('Si necesitas modificar tu reserva, por favor contáctanos directamente.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'table_number' => $this->reservation->table->number,
            'reservation_time' => $this->reservation->reservation_time,
            'status' => 'confirmed'
        ];
    }
}
