<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\DegreeCourses;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
      'role_id',
      // 'facultyID',
      'campus_id',
      'college_id',
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

    public function roles()
    {
      return $this->belongsTo(Roles::class, "role_id", "id");
    }

    public function campuses()
    {
      return $this->belongsTo(Campuses::class, "campus_id", "id");
    }

    public function colleges()
    {
      return $this->belongsTo(Colleges::class, "college_id", "id");
    }

    public function degree_courses()
    {
      return $this->belongsTo(DegreeCourses::class, "degree_course_id", "id");
    }
}
