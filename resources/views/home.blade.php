@extends('layouts.app')

@section('title', 'Vibon Home')

@section('content')
<div class="container">
    <a href="/vibe/create">Start a vibe</a>
    <br><br>

    <form action="/UserVibe">
        <input type="text" name="join-vibe" placeholder="Join a vibe..">
        <input type="submit" name="join-submit" value="Join">
    </form>

    <br><br>

    @if(count($vibes) > 0)
        @foreach($vibes as $vibe)
            <a href="/vibe/{{ $vibe->id }}">{{ $vibe->title }}</a>
            <br>
        @endforeach
    @endif
</div>
@endsection
