<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Notification;

class ReservationStageAdvancedNotification extends Notification
{
    public function __construct(
        public Reservation $reservation,
        public string $fromStage,
        public string $toStage,
        public ?string $note = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $fromLabel = $this->stageLabel($this->fromStage);
        $toLabel   = $this->stageLabel($this->toStage);

        $message = "Your reservation '{$this->reservation->title_name}' has been approved by {$fromLabel} and is now with {$toLabel}.";
        if ($this->note) {
            $message .= " Note: {$this->note}";
        }

        return [
            'reservation_id' => $this->reservation->id,
            'title'          => $this->reservation->title_name,
            'date'           => $this->reservation->date,
            'time_start'     => $this->reservation->time_start,
            'time_end'       => $this->reservation->time_end,
            'current_stage'  => $this->toStage,
            'note'           => $this->note,
            'message'        => $message,
            'type'           => 'stage_advanced',
        ];
    }

    private function stageLabel(string $stage): string
    {
        return match ($stage) {
            'student_director' => 'Student Director',
            'vp_approval'      => 'VP Office',
            'campus_director'  => 'Campus Director',
            'admin'            => 'Admin',
            default            => ucfirst($stage),
        };
    }
}
