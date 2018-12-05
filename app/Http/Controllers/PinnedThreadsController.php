<?php

namespace App\Http\Controllers;

use App\Thread;

class PinnedThreadsController extends Controller
{
    public function store(Thread $thread)
    {
        $thread->update(['pinned' => true]);
    }

    public function destroy(Thread $thread)
    {
        $thread->update(['pinned' => false]);
    }
}
