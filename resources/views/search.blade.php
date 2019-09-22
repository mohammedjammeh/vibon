@extends('layouts.app')
@section('title', 'Vibon Search')
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
                            @if($notification->data['response'])
                                <p>Your request to join '{{ $vibe->name }}' has been accepted.</p>
                            @else
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

            <h3>Search Results..</h3>
                <div class="api-tracks">
                    @foreach($apiTracks as $apiTrack)
                        <div class="playback-play-track-search">
                            <a href="#">
                                <img src="{{ $apiTrack->album->images[0]->url }}">

                                <span class="track-api-id" hidden>{{ $apiTrack->id }}</span>
                                <span class="track-vibon-id" hidden>{{ $apiTrack->vibon_id ?? null }}</span>
                                <span class="track-uri" hidden>{{ $apiTrack->uri }}</span>
                            </a>
                            <p style="white-space: nowrap; overflow: hidden;">{{ $apiTrack->name }}</p>

                            @foreach($user['vibes'] as $userVibe)
                                @if(in_array($userVibe->id, $apiTrack->vibes))
                                    <form method="POST" action="{{ route('track-vibe.destroy', ['vibe' => $userVibe->id, 'track' => $apiTrack->vibon_id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="submit" name="track-vibe-destroy" value="{{ $userVibe->name }}" style="background:red;">
                                    </form>
                                    <br>
                                @else
                                    <form method="POST" action="{{ route('track-vibe.store', ['vibe' => $userVibe->id]) }}">
                                        @csrf
                                        <input type="hidden" name="track-api-id" value="{{ $apiTrack->id }}">
                                        <input type="submit" name="track-vibe-store" value="{{ $userVibe->name }}">
                                    </form>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
        @endif
    </div>
@endsection
