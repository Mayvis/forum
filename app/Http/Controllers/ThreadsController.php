<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Channel;
use App\Trending;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use App\Filters\ThreadFilters;
use Illuminate\Validation\Rule;

class ThreadsController extends Controller
{
    /**
     * Create a new ThreadsController instance.
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Channel $channel
     * @param ThreadFilters $threadFilters
     * @param Trending $trending
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel, ThreadFilters $threadFilters, Trending $trending)
    {
        $threads = $this->getThreads($channel, $threadFilters);

        if (\request()->wantsJson()) {
            return $threads;
        }

        return view('threads.index', [
            'threads' => $threads,
            'channel' => $channel->get(),
            'trending' => $trending->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create', [
            'channels' => Channel::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Recaptcha $recaptcha
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Recaptcha $recaptcha)
    {
        $request->validate([
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => [
                'required',
                Rule::exists('channels', 'id')->where(function ($query) {
                    $query->where('archived', false);
                })
            ],
            'g-recaptcha-response' => ['required', $recaptcha],
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => \request('channel_id'),
            'body' => \request('body'),
            'title' => \request('title'),
        ]);

        if ($request->wantsJson()) {
            return response($thread, 201);
        }

        return redirect($thread->path())
            ->with('flash', 'Your thread has been published!');
    }

    /**
     * Display the specified resource.
     *
     * @param $channelId
     * @param  \App\Thread $thread
     * @param Trending $trending
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread, Trending $trending)
    {
        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $trending->push($thread);

        $thread->increment('visits');

        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $channelId
     * @param  \App\Thread $thread
     * @return Thread
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($channelId, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->update(\request()->validate([
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
        ]));

        return $thread;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $channel
     * @param  \App\Thread $thread
     * @return void
     * @throws \Exception
     */
    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if (\request()->wantsJson()) {
            return response([], '204');
        }

        return redirect('/threads');
    }

    /**
     * Fetch all relevant threads.
     *
     * @param Channel $channel
     * @param ThreadFilters $threadFilters
     * @return mixed
     */
    protected function getThreads(Channel $channel, ThreadFilters $threadFilters)
    {
        $threads = Thread::orderBy('pinned', 'DESC')
            ->latest()
            ->filter($threadFilters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }

        return $threads->paginate(25);
    }
}
