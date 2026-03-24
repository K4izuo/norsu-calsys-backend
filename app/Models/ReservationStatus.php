<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationStatus extends Model
{
    protected $table = 'reservation_statuses';

    protected $fillable = [
        'reservation_id',
        'moved_by_user',
        'move_reason',
        'old_date',
        'old_time_start',
        'old_time_end',
        'new_date',
        'new_time_start',
        'new_time_end',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function movedByUser()
    {
        return $this->belongsTo(User::class, 'moved_by_user');
    }
}
