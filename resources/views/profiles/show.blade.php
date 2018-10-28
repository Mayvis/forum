@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h1>
                            {{ $profileUser->name }}
                            <small>since {{ $profileUser->created_at->diffForHumans() }}</small>
                        </h1>
                    </div>
                </div>


                @foreach($activities as $date => $activity)
                    <h3>{{ $date }}</h3>
                    <hr>
                    @foreach($activity as $record)
                        @include("profiles.activities.{$record->type}", ['activity' => $record])
                    @endforeach
                @endforeach

                {{--{{ $threads->links() }}--}}

            </div>
        </div>
    </div>
@endsection
