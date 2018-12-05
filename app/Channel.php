<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 */
class Channel extends Model
{
    protected $guarded = [];

    protected $casts = [
        'archived' => 'boolean',
    ];

    /**
     * Get the route key name for laravel.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * A channel consists of thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany('App\Thread');
    }

    /**
     *  A channel can be archived by the administrator.
     */
    public function archive()
    {
        $this->update(['archived' => true]);
    }
}
