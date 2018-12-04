<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Trending;

class SearchController extends Controller
{
    /**
     * Show the search result.
     * @param Trending $trending
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Trending $trending)
    {
        if (request()->wantsJson()) {
            return Thread::search(request('q'))->paginate('25');
        }

        return view('threads.search', [
            'trending' => $trending->get(),
        ]);
    }
}
