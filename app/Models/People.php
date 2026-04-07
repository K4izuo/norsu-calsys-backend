<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'personName',
        'userLinkId',
        'linkTimestamp',
        'linkedByUserId',
    ];

    public function linkedUser()
    {
        return $this->belongsTo(User::class, 'userLinkId');
    }

    public function linkedByUser()
    {
        return $this->belongsTo(User::class, 'linkedByUserId');
    }

    public function taggings()
    {
        return $this->hasMany(Tagging::class, 'tagPeopleID');
    }
}
