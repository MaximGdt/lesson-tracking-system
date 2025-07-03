<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'date',
        'name',
        'name_en',
        'type',
        'is_day_off',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
        'is_day_off' => 'boolean',
    ];

    /**
     * Get type display name.
     */
    public function getTypeDisplayAttribute(): string
    {
        $types = [
            'public' => 'Государственный праздник',
            'observance' => 'Памятная дата',
            'school' => 'Школьный праздник',
            'optional' => 'Дополнительный выходной',
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * Scope for day off holidays.
     */
    public function scopeDayOff($query)
    {
        return $query->where('is_day_off', true);
    }

    /**
     * Scope for holidays in date range.
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Check if date is holiday.
     */
    public static function isHoliday($date): bool
    {
        return self::where('date', $date)->exists();
    }

    /**
     * Check if date is day off.
     */
    public static function isDayOff($date): bool
    {
        return self::where('date', $date)->where('is_day_off', true)->exists();
    }
}