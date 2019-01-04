@extends('layouts.app')

@section('title', 'Vibon Home')

@section('content')
<div class="container">
    <a href="/vibe/create">Start a vibe</a>
    <br>
    <a href="#">Join a vibe</a>

    <form action="#">
        <input type="text" name="join-vibe" placeholder="Join a vibe..">
        <input type="submit" name="join-submit" value="Join">
    </form>

    <br><br>

    @if(count($vibes) > 0)
        @foreach($vibes as $vibe)
            <a href="/vibe/{{ $vibe->id }}">{{ $vibe->title }}</a>
        @endforeach
    @endif
</div>
@endsection
