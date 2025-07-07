<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            HolidaySeeder::class,
            RolesTableSeeder::class,
        ]);

        // Create test data in development
        if (app()->environment('local')) {
            $this->createTestData();
        }
    }

    /**
     * Create test data for development.
     */
    protected function createTestData(): void
    {
        // Create test groups
        $groups = [
            [
                'name' => 'Информационные технологии',
                'code' => 'ИТ-21',
                'course' => 3,
                'speciality' => 'Компьютерные науки',
            ],
            [
                'name' => 'Экономика и финансы',
                'code' => 'ЭФ-22',
                'course' => 2,
                'speciality' => 'Экономика',
            ],
            [
                'name' => 'Менеджмент',
                'code' => 'МН-23',
                'course' => 1,
                'speciality' => 'Менеджмент',
            ],
        ];

        foreach ($groups as $groupData) {
            $group = \App\Models\Group::create($groupData);

            // Create test students for each group
            for ($i = 1; $i <= 20; $i++) {
                \App\Models\Student::create([
                    'external_id' => $group->code . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'first_name' => 'Студент' . $i,
                    'last_name' => 'Фамилия' . $i,
                    'middle_name' => 'Отчество' . $i,
                    'email' => strtolower($group->code) . '-student' . $i . '@example.com',
                    'group_id' => $group->id,
                ]);
            }
        }

        // Assign teachers to groups
        $teachers = \App\Models\User::teachers()->get();
        $subjects = [
            'Программирование',
            'Базы данных',
            'Веб-разработка',
            'Экономическая теория',
            'Финансовый анализ',
            'Менеджмент организации',
        ];

        foreach ($teachers as $index => $teacher) {
            $groupsToAssign = \App\Models\Group::inRandomOrder()->take(2)->get();
            foreach ($groupsToAssign as $group) {
                $group->teachers()->attach($teacher->id, [
                    'subject' => $subjects[$index % count($subjects)]
                ]);
            }
        }

        // Create test schedules for the current month
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        foreach (\App\Models\Group::all() as $group) {
            $groupTeachers = $group->teachers;

            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                // Create 2-3 lessons per day
                $lessonsPerDay = rand(2, 3);
                $startTime = 9; // Start at 9:00

                for ($i = 0; $i < $lessonsPerDay; $i++) {
                    $teacher = $groupTeachers->random();
                    $schedule = \App\Models\Schedule::create([
                        'group_id' => $group->id,
                        'teacher_id' => $teacher->id,
                        'subject' => $teacher->pivot->subject,
                        'date' => $date->format('Y-m-d'),
                        'start_time' => sprintf('%02d:00', $startTime),
                        'end_time' => sprintf('%02d:30', $startTime + 1),
                        'room' => rand(100, 500),
                        'type' => ['lecture', 'practice', 'lab'][rand(0, 2)],
                    ]);

                    // Create lesson record
                    $lesson = $schedule->lesson()->create(['is_conducted' => false]);

                    // Mark some past lessons as conducted
                    if ($date->isPast() && rand(0, 100) < 80) {
                        $lesson->markAsConducted(
                            $teacher,
                            'Занятие прошло успешно',
                            rand(15, 20)
                        );
                    }

                    $startTime += 2; // Next lesson starts 2 hours later
                }
            }
        }

        $this->command->info('Test data created successfully!');
    }
}
