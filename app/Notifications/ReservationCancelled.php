<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class ReservationCancelled extends Notification implements ShouldQueue
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
        $dateFormatted = Carbon::parse($reservation->reservation_time)->format('d/m/Y');
        $timeFormatted = Carbon::parse($reservation->reservation_time)->format('H:i');
        
        return (new MailMessage)
            ->subject('Reserva Cancelada - Restaurante')
            ->greeting('Hola, ' . $reservation->customer_name)
            ->line('Te informamos que la reserva para el día ' . $dateFormatted . ' a las ' . $timeFormatted . ' ha sido cancelada.')
            ->line('Si deseas realizar una nueva reserva, puedes hacerlo a través de nuestra página web o contactándonos directamente.')
            ->action('Hacer nueva reserva', url('/reservations/create'))
            ->line('Lamentamos cualquier inconveniente que esto pueda causar.');
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
            'table_number' => $this->reservation->table->number ?? 'No asignada',
            'reservation_time' => $this->reservation->reservation_time,
            'status' => 'cancelled'
        ];
    }
}
