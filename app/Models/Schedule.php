<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'group_id',
        'teacher_id',
        'subject',
        'date',
        'start_time',
        'end_time',
        'room',
        'type',
        'notes',
        'is_cancelled',
        'cancellation_reason',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_cancelled' => 'boolean',
    ];

    /**
     * Group relationship.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Teacher relationship.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Lesson relationship.
     */
    public function lesson(): HasOne
    {
        return $this->hasOne(Lesson::class);
    }

    /**
     * Get type display name.
     */
    public function getTypeDisplayAttribute(): string
    {
        $types = [
            'lecture' => 'Лекция',
            'practice' => 'Практика',
            'lab' => 'Лабораторная',
            'exam' => 'Экзамен',
            'consultation' => 'Консультация',
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * Check if schedule is in the past.
     */
    public function isPast(): bool
    {
        return $this->date->lt(today());
    }

    /**
     * Check if schedule is today.
     */
    public function isToday(): bool
    {
        return $this->date->isToday();
    }

    /**
     * Check if schedule is in the future.
     */
    public function isFuture(): bool
    {
        return $this->date->gt(today());
    }

    /**
     * Check if lesson was conducted.
     */
    public function isConducted(): bool
    {
        return $this->lesson && $this->lesson->is_conducted;
    }

    /**
     * Get formatted time range.
     */
    public function getTimeRangeAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Scope for not cancelled schedules.
     */
    public function scopeNotCancelled($query)
    {
        return $query->where('is_cancelled', false);
    }

    /**
     * Scope for cancelled schedules.
     */
    public function scopeCancelled($query)
    {
        return $query->where('is_cancelled', true);
    }

    /**
     * Scope for date range.
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for teacher's schedules.
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope for group's schedules.
     */
    public function scopeForGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Scope for upcoming schedules.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', today())->orderBy('date')->orderBy('start_time');
    }
}