<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ThreadWasPublished
{
    use SerializesModels;

    public $thread;

    /**
     * Create a new event instance.
     *
     * @param $thread
     */
    public function __construct($thread)
    {
        $this->thread = $thread;
    }

    /**
     * Get the subject of the event.
     *
     * @return mixed
     */
    public function subject()
    {
        return $this->thread;
    }
}
