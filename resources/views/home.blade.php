@extends('layouts.app')
@section('title', 'Vibon Home')
@section('content')
    <div class="container">
        @if(session('message'))
            <p>{{ session('message') }}</p>
        @endif
    
        @if(!empty($user))
            @if(count($user['vibes']) > 0)
                <h3>My Vibes</h3>
                @foreach($user['vibes'] as $vibe)
                    @if($vibe->pivot->owner)
                        <a href="{{ route('vibe.show', ['id' => $vibe->id]) }}">
                            {{ $vibe->name }}
                            @if(count($vibe->joinRequests) > 0)
                                ({{ count($vibe->joinRequests) }})
                            @endif
                        </a>
                        <br>
                    @endif
                @endforeach
                <br><br><br>
            @endif

            <h3>Random Tracks</h3>
            {{--@include('includes.tracks')--}}
        @endif
    </div>
@endsection
