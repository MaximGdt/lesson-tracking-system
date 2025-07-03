<?php

namespace App\Events;

use App\Models\Schedule;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScheduleChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct(Schedule $schedule, string $action)
    {
        $this->schedule = $schedule;
        $this->action = $action; // created, updated, cancelled
    }
}