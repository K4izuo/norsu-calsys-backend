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
        "user_id",
        "role_id",
        "office_pap_code",
        "office_pap_no",
        "office_c_show",
        "office_c_is_college",
        "office_c_is_one",
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function role()
    {
        return $this->belongsTo(Roles::class, "role_id", "id");
    }
}
