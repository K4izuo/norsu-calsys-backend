<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'event_name',
        'asset_id',
        'range',
        'start_time',
        'end_time',
        'description',
        'people_tag',
        'information_type',
        'category',
        'status',
    ];

    public function asset()
    {
        return $this->belongsTo(Assets::class);
    }
}
