<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
use App\Models\Roles;

class Offices extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        "office_code",
        "office_name",
        "office_acr",
        "office_pap_code",
        "office_pap_no",
        "office_c_show",
        "office_c_is_college",
        "office_c_is_one",
        "oversight_vp_id",
    ];

    public function oversightVp()
    {
        return $this->belongsTo(User::class, 'oversight_vp_id');
    }

    public function headOfOfficeUsers()
    {
        return $this->hasMany(User::class, 'office_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campuses::class);
    }

    public function degreeCourses()
    {
        return $this->hasMany(DegreeCourses::class, 'office_id');
    }
}
