<?php

namespace App;

use Laravel\Scout\Searchable;
use App\Events\ThreadReceivedNewReply;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed channel
 * @property mixed user_id
 * @property mixed subscriptions
 * @property mixed updated_at
 * @property mixed title
 * @property mixed slug
 * @property mixed best_reply_id
 * @property mixed locked
 */
class Thread extends Model
{
    use RecordsActivity, Searchable;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    protected $casts = [
        'locked' => 'boolean',
        'pinned' => 'boolean',
    ];

    /**
     * When sql the thread also count the replies.
     *
     * When delete thread also delete the replies that are associated.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();

            Reputation::reduce($thread->creator, Reputation::THREAD_WAS_PUBLISHED);
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);

            Reputation::award($thread->creator, Reputation::THREAD_WAS_PUBLISHED);
        });
    }

    /**
     * Get a string path for the thread.
     *
     * @return string
     */
    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    /**
     * A thread belongs to a thread.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A thread is assigned a channel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * A thread may have many replies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
    /**
     * Add a reply to the thread.
     *
     * @param $reply
     * @return Model
     */
    public function addReply($reply)
    {
        $reply = $this->replies()->create($reply);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    /**
     * Apply all relevant thread filters.
     *
     * @param $query
     * @param $threadFilters
     * @return mixed
     */
    public function scopeFilter($query, $threadFilters)
    {
        return $threadFilters->apply($query);
    }

    /**
     * Subscribe a user to the current thread.
     *
     * @param null $userId
     * @return $this
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id(),
        ]);

        return $this;
    }

    /**
     * Unsubscribe a user from the current thread.
     *
     * @param null $userId
     */
    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    /**
     * A thread can have many subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    /**
     * Determine if the current user is subscribed to the thread.
     * @return bool
     */
    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    /**
     * Determine if the thread has been updated since the user last read it.
     *
     * @param $user
     * @return bool
     * @throws \Exception
     */
    public function hasUpdatesFor($user)
    {
        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    /**
     * Get the route key name.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Set the proper slug attribute.
     *
     * @param $value
     * @return string
     */
    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);

        while (static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-".$this->id;
        }

        return $this->attributes['slug'] = $slug;
    }

    /**
     * Mark the given reply as the best array.
     *
     * @param Reply $reply
     */
    public function markBestReply(Reply $reply)
    {
        $reply->thread->update(['best_reply_id' => $reply->id]);

        Reputation::award($reply->owner, Reputation::BEST_REPLY_AWARDED);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->toArray() + ['path' => $this->path()];
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
