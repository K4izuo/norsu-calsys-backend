<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\DegreeCourses;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Reservation;

class User extends Authenticatable
{
  use HasFactory, Notifiable, HasApiTokens;

  protected $fillable = [
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'username',
    'password',
    'campus_id',
    'degree_course_id',
  ];

  protected $hidden = [
    'password',
    'remember_token',
  ];

  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  // Add many-to-many relationship with offices
  public function offices()
  {
    return $this->belongsToMany(Offices::class, 'office_user', 'user_id', 'office_id')
      ->withTimestamps();
  }

  // Add accessor to get the first office_id (for users with one office)
  public function getOfficeIdAttribute()
  {
    // Get the first office relationship
    if (!$this->relationLoaded('offices')) {
      $this->load('offices');
    }

    return $this->offices->first()?->id;
  }

  public function campuses()
  {
    return $this->belongsTo(Campuses::class, "campus_id", "id");
  }

  public function degree_courses()
  {
    return $this->belongsTo(DegreeCourses::class, "degree_course_id", "id");
  }

  public function reservations()
  {
    return $this->hasMany(Reservation::class, "reseravtion_id", "id");
  }

  public function userRole()
  {
    return $this->hasOne(UserRoles::class, 'user_id');
  }

  public function assets()
  {
    return $this->hasMany(Assets::class, 'created_by');
  }

  private function getRoleId(): ?int
  {
    if (!$this->relationLoaded('userRole')) {
      $this->load('userRole');
    }
    return $this->userRole?->role_id;
  }

  public function isAdmin()
  {
    return $this->getRoleId() === 4;
  }

  public function isSuperAdmin()
  {
    return $this->getRoleId() === 5;
  }

  public function isDean()
  {
    return $this->getRoleId() === 2;
  }

  public function isStaff()
  {
    return $this->getRoleId() === 3;
  }

  public function canViewAllOffices()
  {
    return $this->isAdmin() || $this->isSuperAdmin();
  }

  public function canViewAllAssets()
  {
    return $this->isAdmin() || $this->isSuperAdmin();
  }
}
