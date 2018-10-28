<div class="card mb-3">
    <div class="card-header">
        <div class="level">
            <h6 class="flex">
                <a href="{{ route('profile', $reply->owner->name) }}">{{ $reply->owner->name }}</a> said
                {{ $reply->created_at->diffForHumans() }}...
            </h6>

            <div>
                <form action="/replies/{{ $reply->id }}/favorites" method="POST">
                    @csrf

                    <button type="submit" class="btn btn-default" {{ $reply->isFavorited() ? 'disabled' : '' }}>
                        {{ $reply->favorites_count }} {{ str_plural('Favorite', $reply->favorites_count) }}
                    </button>
                </form>
            </div>

        </div>
    </div>

    <div class="card-body">
        {{ $reply->body }}
    </div>
</div>
