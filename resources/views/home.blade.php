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

            <h1>My Vibes</h1>
            @foreach($homeContent['user'][0]['vibes'] as $vibe)
                 @if($vibe->pivot->owner == 1)
                    <a href="/vibe/{{ $vibe->id }}">{{ $vibe->title }}</a>
                    <br>
                @endif
            @endforeach

            <br><br>

            <h1>Random Tracks</h1>
            @foreach($homeContent['trackRecommendations'] as $trackRecommendation)
                <img src="{{ $trackRecommendation->album->images[0]->url }}">
                <p>{{ $trackRecommendation->name }}</p>

                @foreach($homeContent['user'][0]['vibes'] as $vibe)

                    <form method="POST" action="/trackvibe">
                        @csrf

                        <div>
                            <input type="hidden" name="api_id" value="{{ $trackRecommendation->id }}">
                        </div>

                        <div>
                            <input type="hidden" name="vibe-id" value="{{ $vibe->id }}">
                        </div>

                        <div>
                            <input type="submit" name="delete-submit" value="{{ $vibe->title }}" style="
                            @for ($track = 0; $track < count($vibe->tracks); $track++)
                                @if($trackRecommendation->id == $vibe->tracks[$track]->api_id)
                                    background:red;
                                    @break
                                @endif
                            @endfor
                            ">
                        </div>
                    </form>
                
                @endforeach

                <br><br><br><br><br>
            @endforeach
        @endif
    </div>
@endsection
