<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStatusHistory extends Model
{
    protected $fillable = [
        'ticket_id',
        'changed_by',
        'from_status',
        'to_status',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // ✅ Status badge color accessor
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->to_status) {
            'open'        => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-purple-100 text-purple-700',
            'pending'     => 'bg-yellow-100 text-yellow-700',
            'resolved'    => 'bg-emerald-100 text-emerald-700',
            'closed'      => 'bg-slate-100 text-slate-600',
            default       => 'bg-gray-100 text-gray-700',
        };
    }

    // ✅ Status icon accessor
    public function getStatusIconAttribute(): string
    {
        return match($this->to_status) {
            'open'        => '🔵',
            'in_progress' => '🟣',
            'pending'     => '🟡',
            'resolved'    => '🟢',
            'closed'      => '⚫',
            default       => '⚪',
        };
    }
}