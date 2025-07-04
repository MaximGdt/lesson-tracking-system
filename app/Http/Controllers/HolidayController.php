<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Get holidays for a specific year.
     */
    public function index(Request $request, $year)
    {
        $holidays = Holiday::whereYear('date', $year)
            ->orderBy('date')
            ->get()
            ->map(function ($holiday) {
                return [
                    'id' => $holiday->id,
                    'date' => $holiday->date->format('Y-m-d'),
                    'name' => app()->getLocale() == 'en' && $holiday->name_en 
                        ? $holiday->name_en 
                        : $holiday->name,
                    'type' => $holiday->type,
                    'is_day_off' => $holiday->is_day_off,
                ];
            });
            
        return response()->json($holidays);
    }
}