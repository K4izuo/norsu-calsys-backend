<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Campuses;

class Colleges extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        "college_name",
        "campus_id",
    ];

    public function campus()
    {
        return $this->belongsTo(Campuses::class, 'campus_id', 'id');
    }
}
