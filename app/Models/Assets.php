<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assets extends Model
{
    // protected $table = 'assets';

    protected $fillable = [
        'asset_name',
        'asset_type',
        'capacity',
        'location',
        'acquisition_date',
        'condition',
        'campus_id',
        'office_id',
        'availability_status',
    ];

    public function campus()
    {
        return $this->belongsTo(Campuses::class);
    }

    public function office()
    {
        return $this->belongsTo(Offices::class);
    }
}
