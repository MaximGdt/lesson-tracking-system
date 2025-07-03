<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone',
        'group_id',
        'birth_date',
        'student_card_number',
        'is_active',
        'additional_data',
        'synced_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'additional_data' => 'array',
        'synced_at' => 'datetime',
    ];

    /**
     * Group relationship.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->last_name . ' ' . $this->first_name;
        if ($this->middle_name) {
            $name .= ' ' . $this->middle_name;
        }
        return $name;
    }

    /**
     * Get the student's short name.
     */
    public function getShortNameAttribute(): string
    {
        $name = $this->last_name . ' ' . mb_substr($this->first_name, 0, 1) . '.';
        if ($this->middle_name) {
            $name .= ' ' . mb_substr($this->middle_name, 0, 1) . '.';
        }
        return $name;
    }

    /**
     * Scope for active students.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if student needs sync (older than 24 hours).
     */
    public function needsSync(): bool
    {
        if (!$this->synced_at) {
            return true;
        }
        
        return $this->synced_at->diffInHours(now()) > 24;
    }
}