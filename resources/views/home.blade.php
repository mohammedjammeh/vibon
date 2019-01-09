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

        <h1>My Vibes</h1>
        @if(count($homeContent['vibes']) > 0)
            @foreach($homeContent['vibes'] as $vibe)
                @if($vibe->pivot->owner == 1)
                    <a href="/vibe/{{ $vibe->id }}">{{ $vibe->title }}</a>
                    <br>
                @endif
            @endforeach
        @endif

        <br><br>

        <h1>Random Tracks</h1>
        @if(count($homeContent['tracks']) > 0)
            @foreach($homeContent['tracks'] as $track)
                <img src="{{ $track->album->images[0]->url }}">
                <p>{{ $track->name }}</p>
                
                @foreach($homeContent['vibes'] as $vibe)
                    <form method="POST" action="/trackvibe">
                        @csrf

                        <div>
                            <input type="hidden" name="vibe-id" value="{{ $vibe->id }}">
                        </div>

                        <!-- <div>
                            <input type="hidden" name="vibe-id" value="{{ $vibe->id }}">
                        </div> -->

                        <div>
                            <input type="submit" name="delete-submit" value="{{ $vibe->title }}">
                        </div>
                    </form>

                @endforeach

                <br><br><br><br>

            @endforeach
        @endif
    </div>
@endsection
