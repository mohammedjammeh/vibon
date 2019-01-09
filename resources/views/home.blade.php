@extends('layouts.app')

@section('title', 'Vibon Home')

@section('content')
    <div class="container">
        <a href="/vibe/create">Start a vibe</a>
        <br><br>

        @if (session('message'))
            <p>{{ session('message') }}</p>
        @endif
        
        <form method="POST" action="/UserVibe">
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

        @if(count($homeContent['vibes']) > 0)
            @foreach($homeContent['vibes'] as $vibe)
                <a href="/vibe/{{ $vibe->id }}">{{ $vibe->title }}</a>
                <br>
            @endforeach
        @endif

        <br><br>

        @if(count($homeContent['tracks']) > 0)
            @foreach($homeContent['tracks'] as $track)
                <p>{{ $track->name }}</p>
            @endforeach
        @endif
    </div>
@endsection
