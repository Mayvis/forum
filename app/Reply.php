<?php

namespace App;

use Carbon\Carbon;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed favorites
 * @property mixed thread
 * @property mixed user_id
 * @property mixed created_at
 * @property mixed body
 * @property mixed owner
 */
class Reply extends Model
{
    use Favoritable, RecordsActivity;

    protected $guarded = [];

    protected $with = ['owner', 'favorites'];

    protected $appends = ['favoritesCount', 'isFavorited', 'isBest'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');

            Reputation::award($reply->owner, Reputation::REPLY_POSTED);
        });

        static::deleted(function ($reply) {
            if ($reply->isBest()) {
                $reply->thread->update(['best_reply_id' => null]);
            }

            $reply->thread->decrement('replies_count');

            Reputation::reduce($reply->owner, Reputation::REPLY_POSTED);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A reply belongs to a thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    /**
     * Determine the path to the reply.
     *
     * @return string
     */
    public function path()
    {
        return $this->thread->path()."#reply-{$this->id}";
    }

    public function mentionedUsers()
    {
        preg_match_all('/@([\w\-\_]+)/', $this->body, $matches);

        return $matches[1];
    }

    /**
     * Set the body attribute.
     * @param $body
     */
    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/@([\w\-\_]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    public function getIsBestAttribute()
    {
        return $this->isBest();
    }

    /**
     * Sanitize the body.
     *
     * @param $body
     * @return mixed
     */
    public function getBodyAttribute($body)
    {
        return Purify::clean($body);
    }
}
