<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationEquipment extends Model
{
    protected $table = 'reservation_equipment';

    protected $fillable = [
        'reservation_id',
        'name',
        'quantity',
        'note',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
