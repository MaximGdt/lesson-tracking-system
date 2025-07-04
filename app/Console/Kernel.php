<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sync holidays at the beginning of each year
        $schedule->command('holidays:sync')->yearlyOn(1, 1, '00:00');
        
        // Also sync holidays in December for next year
        $schedule->command('holidays:sync ' . (now()->year + 1))->yearlyOn(12, 1, '00:00');
        
        // Clean up old lesson logs (older than 1 year)
        $schedule->call(function () {
            \App\Models\LessonLog::where('created_at', '<', now()->subYear())->delete();
        })->monthly();
        
        // Send daily reminder emails to teachers about today's lessons
        $schedule->call(function () {
            $teachers = \App\Models\User::teachers()
                ->whereHas('schedules', function ($query) {
                    $query->whereDate('date', today())
                        ->where('is_cancelled', false);
                })
                ->get();
                
            foreach ($teachers as $teacher) {
                // Send reminder email
                Mail::to($teacher->email)->send(new \App\Mail\DailyScheduleReminder($teacher));
            }
        })->dailyAt('07:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}