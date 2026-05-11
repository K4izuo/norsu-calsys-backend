<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Notification;

class ReservationStageDeclinedNotification extends Notification
{
    public function __construct(
        public Reservation $reservation,
        public string $declinedAtStage,
        public ?string $reason
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $roleLabel = match ($this->declinedAtStage) {
            'student_director' => 'Student Director',
            'vpaa'             => 'VPAA',
            'vpsas'            => 'VPSAS',
            'vpaf'             => 'VPAF',
            'vprde'            => 'VPRDE',
            'campus_director'  => 'Campus Director',
            'admin'            => 'Admin',
            default            => ucfirst($this->declinedAtStage),
        };

        $message = $this->reason
            ? "Your reservation '{$this->reservation->title_name}' was declined by {$roleLabel}. Reason: {$this->reason}"
            : "Your reservation '{$this->reservation->title_name}' was declined by {$roleLabel}.";

        return [
            'reservation_id'    => $this->reservation->id,
            'title'             => $this->reservation->title_name,
            'date'              => $this->reservation->date,
            'time_start'        => $this->reservation->time_start,
            'time_end'          => $this->reservation->time_end,
            'declined_at_stage' => $this->declinedAtStage,
            'declined_by_role'  => $roleLabel,
            'reason'            => $this->reason,
            'message'           => $message,
            'type'              => 'stage_declined',
        ];
    }
}
