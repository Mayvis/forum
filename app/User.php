<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property mixed id
 * @property mixed lastReply
 * @property mixed avatar_path
 * @property bool confirmed
 * @property null confirmation_token
 * @property mixed name
 * @property mixed email
 */
class User extends Authenticatable
{
    use Notifiable, HasReputation;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email', 'email_verified_at',
    ];

    protected $casts = [
        'confirmed' => 'boolean',
        'reputation' => 'integer',
    ];

    protected $appends = [
        'isAdmin'
    ];

    /**
     * Get the route key name for Laravel.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'username';
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    public function read($thread)
    {
        cache()->forever(
            $this->visitedThreadCacheKey($thread),
            Carbon::now()
        );
    }

    public function confirm()
    {
        $this->confirmed = true;

        $this->confirmation_token = null;

        $this->save();
    }

    public function getAvatarPathAttribute($avatar)
    {
        return $avatar ? '/storage/'.$avatar : '/images/avatars/default.png';
    }

    public function visitedThreadCacheKey($thread)
    {
        return sprintf('users.%s.visits.%s', $this->id, $thread->id);
    }

    /**
     * Determine if the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return in_array(
            strtolower($this->email),
            array_map('strtolower', config('forum.administrators'))
        );
    }

    /**
     * Determine if the user is an administrator.
     *
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->isAdmin();
    }
}
