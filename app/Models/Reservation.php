<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Reservation extends Model
{
  use HasFactory, Notifiable;
  
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
    'reserve_by_user',
    'status',
  ];

  public function asset()
  {
    return $this->belongsTo(Assets::class);
  }

  public function user()
  {
    return $this->belongTo(User::class);
  }
}
