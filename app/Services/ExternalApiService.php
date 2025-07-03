<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExternalApiService
{
    protected $apiUrl;
    protected $apiKey;
    protected $holidaysApiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.student_api.url');
        $this->apiKey = config('services.student_api.key');
        $this->holidaysApiUrl = config('services.holidays_api.url');
    }

    /**
     * Get students by group code from external API.
     */
    public function getStudentsByGroup(string $groupCode): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->apiUrl . '/groups/' . $groupCode . '/students');

            if ($response->successful()) {
                return $response->json('data', []);
            }

            Log::error('Failed to fetch students from API', [
                'group_code' => $groupCode,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Exception while fetching students', [
                'group_code' => $groupCode,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get student by external ID.
     */
    public function getStudentById(string $externalId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->get($this->apiUrl . '/students/' . $externalId);

            if ($response->successful()) {
                return $response->json('data');
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception while fetching student', [
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get Ukrainian holidays for a year.
     */
    public function getHolidays(int $year): array
    {
        $cacheKey = "holidays_{$year}";
        
        return Cache::remember($cacheKey, now()->addMonth(), function () use ($year) {
            try {
                $response = Http::get($this->holidaysApiUrl . $year . '/UA');

                if ($response->successful()) {
                    $holidays = $response->json();
                    
                    // Map API response to our format
                    return array_map(function ($holiday) {
                        return [
                            'date' => $holiday['date'],
                            'name' => $holiday['localName'] ?? $holiday['name'],
                            'name_en' => $holiday['name'],
                            'type' => $this->mapHolidayType($holiday['types'] ?? []),
                            'is_day_off' => in_array('Public', $holiday['types'] ?? []),
                        ];
                    }, $holidays);
                }

                return [];
            } catch (\Exception $e) {
                Log::error('Exception while fetching holidays', [
                    'year' => $year,
                    'error' => $e->getMessage(),
                ]);

                return [];
            }
        });
    }

    /**
     * Map holiday types from API to our types.
     */
    protected function mapHolidayType(array $types): string
    {
        if (in_array('Public', $types)) {
            return 'public';
        } elseif (in_array('School', $types)) {
            return 'school';
        } elseif (in_array('Observance', $types)) {
            return 'observance';
        }

        return 'optional';
    }

    /**
     * Sync holidays for current and next year.
     */
    public function syncHolidays(): void
    {
        $currentYear = now()->year;
        
        foreach ([$currentYear, $currentYear + 1] as $year) {
            $holidays = $this->getHolidays($year);
            
            foreach ($holidays as $holidayData) {
                \App\Models\Holiday::updateOrCreate(
                    ['date' => $holidayData['date']],
                    $holidayData
                );
            }
        }
    }
}