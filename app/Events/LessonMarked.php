<?php

namespace App\Events;

use App\Models\Lesson;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LessonMarked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lesson;

    /**
     * Create a new event instance.
     */
    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }
}