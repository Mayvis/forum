<reply :attributes="{{ $reply }}" inline-template v-cloak>
    <div id="reply-{{ $reply->id }}" class="card mb-3">
        <div class="card-header">
            <div class="level">
                <h6 class="flex">
                    <a href="{{ route('profile', $reply->owner->name) }}">{{ $reply->owner->name }}</a> said
                    {{ $reply->created_at->diffForHumans() }}...
                </h6>

                @if(auth()->check())
                    <div>
                        <favorite :reply="data"></favorite>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body"></textarea>
                </div>

                <button class="btn btn-sm btn-primary" @click="update">Update</button>
                <button class="btn btn-sm btn-link" @click="editing = false">Cancel</button>
            </div>

            <div v-else v-text="body"></div>
        </div>

        @can('update', $reply)
            <div class="card-footer level">
                <button class="btn btn-sm mr-1" @click="editing = true">Edit</button>
                <button class="btn btn-danger btn-sm mr-1" @click="destroy">Delete</button>
            </div>
        @endcan

    </div>
</reply>


