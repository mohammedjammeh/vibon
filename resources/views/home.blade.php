@extends('layouts.app')

@section('title', 'Vibon Home')

@section('content')
    <div class="container">
        <a href="/vibe/create">Start a vibe</a>
        <br><br>

        @if (session('message'))
            <p>{{ session('message') }}</p>
        @endif
        
        <form method="POST" action="/uservibe">
            @csrf

            @if($errors->any())
                <div>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <input type="number" name="key" placeholder="Join a vibe..">

            <input type="submit" name="submit-key" value="Join">
        </form>

        <br><br>

    
        @if(count($homeContent['user']) > 0)

            <h3>My Vibes</h3>
            @foreach($homeContent['user'][0]['vibes'] as $vibe)
                 @if($vibe->pivot->owner == 1)
                    <a href="/vibe/{{ $vibe->id }}">{{ $vibe->title }}</a>
                    <br>
                @endif
            @endforeach

            <br><br>

            <h3>Random Tracks</h3>
            @foreach($homeContent['trackRecommendations'] as $trackRecommendation)
                <img src="{{ $trackRecommendation->album->images[0]->url }}">
                <p>{{ $trackRecommendation->name }}</p>

                @foreach($homeContent['user'][0]['vibes'] as $vibe)

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
