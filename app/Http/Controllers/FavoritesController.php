<?php

namespace App\Http\Controllers;

use App\Reply;
use App\Favorite;
use App\Reputation;

class FavoritesController extends Controller
{
    /**
     * FavoritesController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new favorite in the database.
     *
     * @param Reply $reply
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Reply $reply)
    {
        $reply->favorite();

        Reputation::award($reply->owner, Reputation::REPLY_FAVORITED);

        return back();
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();

        Reputation::reduce($reply->owner, Reputation::REPLY_FAVORITED);
    }
}
