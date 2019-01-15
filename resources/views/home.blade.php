@extends('layouts.app')

@section('title', 'Vibon Home')

@section('content')
    <div class="container">
        <a href="/vibe/create">Start a vibe</a>
        <br><br>

        @if(session('message'))
            <p>{{ session('message') }}</p>
        @endif


        @if(count($homeContent['notifications']) > 0)
            <h3>Notifications</h3>

            @foreach($homeContent['notifications'] as $notification)

                @foreach($homeContent['userVibesTracks']['vibes'] as $vibe)

                    @if($vibe->id == $notification->data['vibe_id'])

                        <p>Your request to join '{{ $vibe->title }}' has been accepted.</p>

                    @endif

                @endforeach

            @endforeach
        @endif

        <br>
        
    
        @if($homeContent['userVibesTracks'])

            <h3>My Vibes</h3>
            @foreach($homeContent['userVibesTracks']['vibes'] as $vibe)
                 @if($vibe->pivot->owner == 1)
                    <a href="/vibe/{{ $vibe->id }}">{{ $vibe->title }}</a>
                    <br>
                @endif
            @endforeach

            <br><br><br>

            <h3>Random Tracks</h3>
            @foreach($homeContent['trackRecommendations'] as $trackRecommendation)
                <img src="{{ $trackRecommendation->album->images[0]->url }}">
                <p>{{ $trackRecommendation->name }}</p>

                @foreach($homeContent['userVibesTracks']['vibes'] as $vibe)

                    <form method="POST" action="/trackvibe">
                        @csrf

                        <input type="hidden" name="track-api-id" value="{{ $trackRecommendation->id }}">

                        <input type="hidden" name="vibe-api-id" value="{{ $vibe->api_id }}">

                        <input type="hidden" name="vibe-id" value="{{ $vibe->id }}">

                        <input type="submit" name="delete-submit" value="{{ $vibe->title }}" style="
                        @for ($track = 0; $track < count($vibe->tracks); $track++)
                            @if($trackRecommendation->id == $vibe->tracks[$track]->api_id)
                                background:red;
                                @break
                            @endif
                        @endfor
                        ">
                    </form>

                    @for ($track = 0; $track < count($vibe->tracks); $track++)
                        @if($trackRecommendation->id == $vibe->tracks[$track]->api_id)
                            <form method="POST" action="/trackvibe/vibe/{{ $vibe->tracks[$track]->pivot->vibe_id }}/track/{{ $vibe->tracks[$track]->pivot->track_id }}">
                                @csrf
                                @method('DELETE')

                                <input type="submit" name="track-vibe-delete" value="Remove">
                            </form>
                            @break
                        @endif
                    @endfor

                    <br>
                @endforeach

                <br><br><br><br><br>
            @endforeach
        @endif
    </div>
@endsection
