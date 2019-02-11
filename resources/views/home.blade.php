@extends('layouts.app')
@section('title', 'Vibon Home')
@section('content')
    <div class="container">
        <a href="{{ route('vibe.create') }}">Start a vibe</a>
        <br><br>

        @if(session('message'))
            <p>{{ session('message') }}</p>
        @endif

        @if(count($user->notifications) > 0)
            <h3>Notifications</h3>
            @foreach($user->notifications as $notification)
                @foreach($vibes as $vibe)
                    @if($vibe->id == $notification->data['vibe_id'])
                        @if($notification->type == 'App\Notifications\ResponseToJoinAVibe')
                            @if($notification->data['response'] == 1)
                                <p>Your request to join '{{ $vibe->name }}' has been accepted.</p>
                            @elseif($notification->data['response'] == 0)
                                <p>Sorry, your request to join '{{ $vibe->name }}' has been rejected.</p>
                            @endif
                        @elseif($notification->type == 'App\Notifications\RemovedFromAVibe')
                            <p>You have been removed from the '{{ $vibe->name }}' vibe.</p>
                        @endif
                    @endif
                @endforeach
            @endforeach
        @endif
        <br>
    
        @if(!empty($user))
            @if(count($user['vibes']) > 0)
                <h3>My Vibes</h3>
                @foreach($user['vibes'] as $vibe)
                    @if($vibe->pivot->owner == 1)
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
            @include('includes.tracks')
        @endif
    </div>
@endsection
