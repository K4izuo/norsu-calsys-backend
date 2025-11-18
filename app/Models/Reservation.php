<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'title_name',
        'asset_id',
        'range',
        'time_start',
        'time_end',
        'description',
        'people_tag',
        'info_type',
        'category',
        'date',
        'status',
    ];

    public function asset()
    {
        return $this->belongsTo(Assets::class);
    }
}
