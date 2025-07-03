<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'schedule_id',
        'is_conducted',
        'marked_at',
        'marked_by',
        'notes',
        'students_present',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_conducted' => 'boolean',
        'marked_at' => 'datetime',
    ];

    /**
     * Schedule relationship.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * User who marked the lesson.
     */
    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /**
     * Logs relationship.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(LessonLog::class);
    }

    /**
     * Get attendance percentage.
     */
    public function getAttendancePercentageAttribute(): ?float
    {
        if (!$this->students_present || !$this->schedule->group->students->count()) {
            return null;
        }

        return round(($this->students_present / $this->schedule->group->students->count()) * 100, 2);
    }

    /**
     * Mark lesson as conducted.
     */
    public function markAsConducted(User $user, ?string $notes = null, ?int $studentsPresent = null): void
    {
        $oldValues = [
            'is_conducted' => $this->is_conducted,
            'marked_at' => $this->marked_at,
            'marked_by' => $this->marked_by,
            'notes' => $this->notes,
            'students_present' => $this->students_present,
        ];

        $this->update([
            'is_conducted' => true,
            'marked_at' => now(),
            'marked_by' => $user->id,
            'notes' => $notes,
            'students_present' => $studentsPresent,
        ]);

        // Create log entry
        $this->logs()->create([
            'user_id' => $user->id,
            'action' => 'marked',
            'old_values' => $oldValues,
            'new_values' => $this->only(['is_conducted', 'marked_at', 'marked_by', 'notes', 'students_present']),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Mark lesson as not conducted.
     */
    public function markAsNotConducted(User $user): void
    {
        $oldValues = [
            'is_conducted' => $this->is_conducted,
            'marked_at' => $this->marked_at,
            'marked_by' => $this->marked_by,
            'notes' => $this->notes,
            'students_present' => $this->students_present,
        ];

        $this->update([
            'is_conducted' => false,
            'marked_at' => null,
            'marked_by' => null,
            'notes' => null,
            'students_present' => null,
        ]);

        // Create log entry
        $this->logs()->create([
            'user_id' => $user->id,
            'action' => 'unmarked',
            'old_values' => $oldValues,
            'new_values' => $this->only(['is_conducted', 'marked_at', 'marked_by', 'notes', 'students_present']),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope for conducted lessons.
     */
    public function scopeConducted($query)
    {
        return $query->where('is_conducted', true);
    }

    /**
     * Scope for not conducted lessons.
     */
    public function scopeNotConducted($query)
    {
        return $query->where('is_conducted', false);
    }
}