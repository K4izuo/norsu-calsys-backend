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

  private function getRoleId(): ?int
  {
    if (!$this->relationLoaded('user_id')) {
      $this->load('user_id');
    }
    return $this->user_id?->role_id;
  }

  public function isAdmin()
  {
    return $this->role_id === 4;
  }

  public function isDean()
  {
    return $this->role_id === 2;
  }

  public function isStaff()
  {
    return $this->role_id === 3;
  }

  public function canViewAllOffices()
  {
    return $this->isAdmin();
  }

  public function canViewAllAssets()
  {
    return $this->isAdmin();
  }
}
