<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requester extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',       // ← added
        'first_name',
        'last_name',
        'email',
        'phone',
        'company',
    ];

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Link to user account
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}