<?php

namespace App;

use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed user
 * @property mixed thread
 */
class ThreadSubscription extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function notify($reply)
    {
        $this->user->notify(New ThreadWasUpdated($this->thread, $reply));
    }
}
