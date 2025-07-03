<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = now()->year;
        
        // Ukrainian holidays for current year
        $holidays = [
            [
                'date' => $currentYear . '-01-01',
                'name' => 'Новий рік',
                'name_en' => 'New Year\'s Day',
                'type' => 'public',
                'is_day_off' => true,
            ],
            [
                'date' => $currentYear . '-01-07',
                'name' => 'Різдво Христове',
                'name_en' => 'Christmas Day',
                'type' => 'public',
                'is_day_off' => true,
            ],
            [
                'date' => $currentYear . '-03-08',
                'name' => 'Міжнародний жіночий день',
                'name_en' => 'International Women\'s Day',
                'type' => 'public',
                'is_day_off' => true,
            ],
            [
                'date' => $currentYear . '-05-01',
                'name' => 'День праці',
                'name_en' => 'Labour Day',
                'type' => 'public',
                'is_day_off' => true,
            ],
            [
                'date' => $currentYear . '-05-09',
                'name' => 'День перемоги',
                'name_en' => 'Victory Day',
                'type' => 'public',
                'is_day_off' => true,
            ],
            [
                'date' => $currentYear . '-06-28',
                'name' => 'День Конституції України',
                'name_en' => 'Constitution Day',
                'type' => 'public',
                'is_day_off' => true,
            ],
            [
                'date' => $currentYear . '-08-24',
                'name' => 'День Незалежності України',
                'name_en' => 'Independence Day',
                'type' => 'public',
                'is_day_off' => true,
            ],
            [
                'date' => $currentYear . '-10-14',
                'name' => 'День захисників і захисниць України',
                'name_en' => 'Defenders Day',
                'type' => 'public',
                'is_day_off' => true,
            ],
            [
                'date' => $currentYear . '-12-25',
                'name' => 'Різдво Христове (католицьке)',
                'name_en' => 'Catholic Christmas Day',
                'type' => 'public',
                'is_day_off' => true,
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(
                ['date' => $holiday['date']],
                $holiday
            );
        }

        // Try to sync more holidays from API
        try {
            app(\App\Services\ExternalApiService::class)->syncHolidays();
        } catch (\Exception $e) {
            $this->command->warn('Could not sync holidays from API: ' . $e->getMessage());
        }

        $this->command->info('Holidays seeded successfully!');
    }
}