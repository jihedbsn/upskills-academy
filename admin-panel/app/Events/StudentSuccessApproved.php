<?php

namespace App\Events;

use App\Models\StudentSuccess;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentSuccessApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $studentSuccess;

    /**
     * Create a new event instance.
     */
    public function __construct(StudentSuccess $studentSuccess)
    {
        $this->studentSuccess = $studentSuccess;
    }
}
