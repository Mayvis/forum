<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class UserAvatarController extends Controller
{

    public function store()
    {
        $this->validate(\request(), [
            'avatars' => ['required', 'image']
        ]);

        auth()->user()->update([
            'avatar_path' => \request()->file('avatars')->store('avatars', 'public')
        ]);

        return response([], 204);

    }
}
