<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Link to requester profile
    public function requester()
    {
        return $this->hasOne(Requester::class);
    }

    // Tickets assigned to this user as agent
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_user_id');
    }

    // Tickets created by this user
    public function createdTickets()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    // Role helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSupervisor()
    {
        return $this->role === 'supervisor';
    }

    public function isAgent()
    {
        return $this->role === 'support_agent';
    }

    public function isRequester()
    {
        return $this->role === 'requester';
    }
}