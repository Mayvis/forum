<?php

namespace App\Http\Controllers\Admin;

use App\Channel;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ChannelsController extends Controller
{
    public function index()
    {
        return view('admin.channels.index')
            ->with('channels', Channel::with('threads')->get());
    }

    public function create()
    {
        return view('admin.channels.create');
    }

    public function edit(Channel $channel)
    {
        return view('admin.channels.edit', compact('channel'));
    }

    public function store()
    {
        $data = request()->validate([
            'name' => 'required|unique:channels',
            'description' => 'required',
        ]);

        $channel = Channel::create($data + ['slug' => str_slug($data['name'])]);

        Cache::forget('channels');

        if (request()->wantsJson()) {
            return response($channel, 201);
        }

        return redirect(route('admin.channels.index'))
            ->with('flash', 'Your channel has been created!');
    }

    /**
     * Update an existing channel.
     *
     * @param Channel $channel
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(Channel $channel)
    {
        $channel->update(
            request()->validate([
                'name' => ['required', Rule::unique('channels')->ignore($channel->id)],
                'description' => 'required',
                'archived' => 'required|boolean',
            ])
        );

        cache()->forget('channels');

        if (request()->wantsJson()) {
            return response($channel, 200);
        }

        return redirect(route('admin.channels.index'))
            ->with('flash', 'Your channel has been updated!');
    }
}
