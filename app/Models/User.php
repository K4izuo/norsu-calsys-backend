<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\DegreeCourses;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Reservation;

class User extends Authenticatable
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable, HasApiTokens;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'first_name',
    'middle_name',
    'last_name',
    'email',
    'username',      // <-- add this
    'password',      // <-- add this
    // 'role_id',
    'campus_id',
    'office_id',
    'degree_course_id',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
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

  // Add this relationship for assets
  public function assets()
  {
    return $this->hasMany(Assets::class, 'created_by');
  }

  // Helper method to get role_id - FIXED!
  private function getRoleId(): ?int
  {
    if (!$this->relationLoaded('userRole')) {
      $this->load('userRole');
    }
    return $this->userRole?->role_id;
  }

  // FIXED - Now uses getRoleId() method
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
