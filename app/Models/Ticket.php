<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'description',
        'status',
        'priority',
        'requester_id',
        'category_id',
        'assigned_user_id',
        'created_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function requester()
    {
        return $this->belongsTo(Requester::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    // -------------------------------------------------------
    // Status Transition Logic
    // -------------------------------------------------------

    /**
     * Returns the allowed next statuses from the current status.
     * Transitions are strictly forward — no going backwards.
     */
    public function getAllowedStatusTransitions(): array
    {
        return match($this->status) {
            'open'        => ['in_progress', 'pending'],
            'in_progress' => ['pending', 'resolved'],
            'pending'     => ['resolved'],
            'resolved'    => ['closed'],
            'closed'      => [],        // terminal — no further changes allowed
            default       => [],
        };
    }

    /**
     * Checks whether the ticket can transition to the given status.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        // Closed tickets cannot be changed under any circumstance
        if ($this->status === 'closed') {
            return false;
        }

        return in_array($newStatus, $this->getAllowedStatusTransitions());
    }

    // -------------------------------------------------------
    // Accessors — Badge Classes
    // -------------------------------------------------------

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'open'        => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-purple-100 text-purple-800',
            'pending'     => 'bg-yellow-100 text-yellow-800',
            'resolved'    => 'bg-green-100 text-green-800',
            'closed'      => 'bg-gray-100 text-gray-800',
            default       => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority) {
            'low'      => 'bg-gray-100 text-gray-800',
            'medium'   => 'bg-blue-100 text-blue-800',
            'high'     => 'bg-orange-100 text-orange-800',
            'critical' => 'bg-red-100 text-red-800',
            default    => 'bg-gray-100 text-gray-800',
        };
    }

        // Add this relationship
    public function statusHistories()
    {
        return $this->hasMany(TicketStatusHistory::class)->orderBy('created_at', 'asc');
    }
}