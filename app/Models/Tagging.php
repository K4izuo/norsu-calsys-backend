<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagging extends Model
{
    protected $table = 'taggings';

    protected $primaryKey = 'tagID';

    protected $fillable = [
        'tagPeopleID',
        'taggedReservationID',
    ];

    public function person()
    {
        return $this->belongsTo(People::class, 'tagPeopleID');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'taggedReservationID');
    }
}
