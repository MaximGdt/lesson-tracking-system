<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'password',
        'phone',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user's full name.
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
     * Get the user's short name (Last Name + First Initial).
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
     * Roles relationship.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Groups relationship (for teachers).
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_teacher', 'teacher_id', 'group_id')
            ->withPivot('subject')
            ->withTimestamps();
    }

    /**
     * Schedules relationship (for teachers).
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    /**
     * Marked lessons relationship.
     */
    public function markedLessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'marked_by');
    }

    /**
     * Lesson logs relationship.
     */
    public function lessonLogs(): HasMany
    {
        return $this->hasMany(LessonLog::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Check if user is teacher.
     */
    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for teachers.
     */
    public function scopeTeachers($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'teacher');
        });
    }
}