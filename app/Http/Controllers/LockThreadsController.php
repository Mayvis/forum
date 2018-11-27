<?php

namespace App\Http\Controllers;

use App\Thread;
use Illuminate\Http\Request;

class LockThreadsController extends Controller
{
    /**
     * @param Thread $thread
     * @return void
     */
    public function store(Thread $thread)
    {
        $thread->lock();
    }
}
