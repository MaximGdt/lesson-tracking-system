<?php

namespace App\Mail;

use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LessonMarkedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lesson;

    /**
     * Create a new message instance.
     */
    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Занятие отмечено как проведенное',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.lesson-marked',
            with: [
                'teacherName' => $this->lesson->schedule->teacher->full_name,
                'groupName' => $this->lesson->schedule->group->name,
                'subject' => $this->lesson->schedule->subject,
                'date' => $this->lesson->schedule->date->format('d.m.Y'),
                'time' => $this->lesson->schedule->time_range,
                'markedBy' => $this->lesson->markedBy->full_name,
                'markedAt' => $this->lesson->marked_at->format('d.m.Y H:i'),
            ],
        );
    }
}