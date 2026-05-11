<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Notification;

class ReservationFullyApprovedNotification extends Notification
{
    public function __construct(
        public Reservation $reservation,
        public ?string $note = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $message = "Your reservation '{$this->reservation->title_name}' has been fully approved and is now on the calendar.";
        if ($this->note) {
            $message .= " Note: {$this->note}";
        }

        return [
            'reservation_id' => $this->reservation->id,
            'title'          => $this->reservation->title_name,
            'date'           => $this->reservation->date,
            'time_start'     => $this->reservation->time_start,
            'time_end'       => $this->reservation->time_end,
            'note'           => $this->note,
            'message'        => $message,
            'type'           => 'fully_approved',
        ];
    }
}
