<?php

namespace App\Listeners;

use App\Events\LessonMarked;
use App\Mail\LessonMarkedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendLessonNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(LessonMarked $event): void
    {
        $lesson = $event->lesson;
        $schedule = $lesson->schedule;
        
        // Send email to admins
        $admins = \App\Models\User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['super_admin', 'admin']);
        })->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new LessonMarkedMail($lesson));
        }

        // Log the action
        activity()
            ->performedOn($lesson)
            ->causedBy($lesson->markedBy)
            ->withProperties([
                'schedule_id' => $schedule->id,
                'group' => $schedule->group->code,
                'subject' => $schedule->subject,
                'date' => $schedule->date->format('Y-m-d'),
            ])
            ->log('Занятие отмечено как проведенное');
    }
}