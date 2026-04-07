<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Notification;

class ReservationDeclinedNotification extends Notification
{
    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'title'          => $this->reservation->title_name,
            'date'           => $this->reservation->date,
            'time_start'     => $this->reservation->time_start,
            'time_end'       => $this->reservation->time_end,
            'message'        => "Your reservation '{$this->reservation->title_name}' has been declined.",
            'type'           => 'reservation_declined',
        ];
    }
}
