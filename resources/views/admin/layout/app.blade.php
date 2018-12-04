@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-2">
                <ul class="nav nav-pills nav-stacked">
                    <li role="presentation" class="nav-item"><a href="{{ route('admin.dashboard.index') }}" class="nav-link">Dashboard</a></li>
                    <li role="presentation" class="nav-item"><a href="{{ route('admin.channels.index') }}" class="nav-link">Channels</a></li>
                </ul>
            </div>
            <div class="col-md-10">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @yield('administration-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
