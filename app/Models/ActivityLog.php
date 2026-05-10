<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
    ];

    // Relationship to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Static helper to log activities easily
    public static function log(
        string $action,
        string $description,
        string $modelType = null,
        int $modelId = null
    ): void {
        static::create([
            'user_id'     => auth()->id(),
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'description' => $description,
            'ip_address'  => request()->ip(),
        ]);
    }

    // Action badge color
    public function getActionColorAttribute(): string
    {
        return match(true) {
            str_contains($this->action, 'created') => 'bg-green-100 text-green-800',
            str_contains($this->action, 'updated') => 'bg-blue-100 text-blue-800',
            str_contains($this->action, 'deleted') => 'bg-red-100 text-red-800',
            str_contains($this->action, 'login')   => 'bg-purple-100 text-purple-800',
            str_contains($this->action, 'logout')  => 'bg-gray-100 text-gray-800',
            str_contains($this->action, 'reply')   => 'bg-yellow-100 text-yellow-800',
            default                                 => 'bg-gray-100 text-gray-800',
        };
    }

    // Action icon
    public function getActionIconAttribute(): string
    {
        return match(true) {
            str_contains($this->action, 'created') => '➕',
            str_contains($this->action, 'updated') => '✏️',
            str_contains($this->action, 'deleted') => '🗑️',
            str_contains($this->action, 'login')   => '🔐',
            str_contains($this->action, 'logout')  => '🚪',
            str_contains($this->action, 'reply')   => '💬',
            default                                 => '📋',
        };
    }
}