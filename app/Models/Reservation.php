<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Reservation extends Model
{
  use HasFactory, Notifiable;

  protected $appends = ['move_reason'];

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
    'other_category',
    'outsource',
    'date',
    'reserved_by_user',
    'approved_by_user',
    'declined_by_user',
    'status',
    'is_moved',
    'original_date',
    'involves_students',
    'requires_vpaa',
    'requires_vpsas',
    'requires_vpaf',
    'requires_vprde',
    'current_stage',
    'declined_at_stage',
    'campus_director_action',
    'multimedia_comment',
    'requestor_type',
    'student_sub_type',
    'student_org_name',
    'csg_name',
    'requestor_tagged',
    'proof_of_request',
    'proof_of_approval',
  ];

  protected $casts = [
    'requestor_tagged' => 'array',
  ];

  public function asset()
  {
    return $this->belongsTo(Assets::class);
  }

  public function reservedByUser()
  {
    return $this->belongsTo(User::class, 'reserved_by_user');
  }

  public function approvedByUser()
  {
    return $this->belongsTo(User::class, 'approved_by_user');
  }

  public function declinedByUser()
  {
    return $this->belongsTo(User::class, 'declined_by_user');
  }

  public function latestStatus()
  {
    return $this->hasOne(ReservationStatus::class)->latest();
  }

  public function taggings()
  {
    return $this->hasMany(\App\Models\Tagging::class, 'taggedReservationID');
  }

  public function equipment()
  {
    return $this->hasMany(ReservationEquipment::class);
  }

  public function guests()
  {
    return $this->hasMany(ReservationGuest::class);
  }

  public function approvals()
  {
    return $this->hasMany(ReservationApproval::class);
  }

  public function getMoveReasonAttribute(): ?string
  {
    return $this->latestStatus?->move_reason;
  }
}
