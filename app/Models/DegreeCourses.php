<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DegreeCourses extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        "office_id",
        "degree_name",
        "degree_acr",
        // "college_id",
        "degree_inp_usr_no",
        "degree_inp_timestamp",
        "degree_upd_usr_no",
        "degree_upd_timestamp",
    ];

    public function office()
    {
        return $this->belongsTo(Offices::class, 'office_id');
    }
}
