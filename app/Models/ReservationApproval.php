<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'stage',
        'user_id',
        'action',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
