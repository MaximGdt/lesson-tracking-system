<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyScheduleReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $teacher
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('app.daily_schedule_reminder'),
        );
    }

    public function content(): Content
    {
        $schedules = $this->teacher->schedules()
            ->whereDate('date', today())
            ->notCancelled()
            ->orderBy('start_time')
            ->get();

        return new Content(
            view: 'emails.daily-schedule-reminder',
            with: [
                'teacher' => $this->teacher,
                'schedules' => $schedules,
            ],
        );
    }
}