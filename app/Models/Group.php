<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'course',
        'speciality',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Teachers relationship.
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_teacher', 'group_id', 'teacher_id')
            ->withPivot('subject')
            ->withTimestamps();
    }

    /**
     * Students relationship.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Schedules relationship.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Get active students count.
     */
    public function getActiveStudentsCountAttribute(): int
    {
        return $this->students()->where('is_active', true)->count();
    }

    /**
     * Scope for active groups.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for groups by course.
     */
    public function scopeByCourse($query, int $course)
    {
        return $query->where('course', $course);
    }

    /**
     * Check if group has a specific teacher.
     */
    public function hasTeacher(User $teacher): bool
    {
        return $this->teachers()->where('teacher_id', $teacher->id)->exists();
    }

    /**
     * Get teacher for specific subject.
     */
    public function getTeacherForSubject(string $subject): ?User
    {
        return $this->teachers()->wherePivot('subject', $subject)->first();
    }
}