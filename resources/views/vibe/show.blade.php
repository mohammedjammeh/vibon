@extends('layouts.app')

@section('title', 'Start a vibe')

@section('content')
    <div class="container">
        <p>{{ $vibe->title }}</p>
        <p>{{ $vibe->description }}</p>
        <p>{{ $vibe->key }}</p>

        <a href="/vibe/{{ $vibe->id }}/edit">Edit</a>

        <form method="POST" action="/vibe/{{ $vibe->id }}">
            @csrf
            @method('DELETE')

            <div>
                <input type="submit" name="delete-submit" value="Delete">
            </div>
        </form>
    </div>
@endsection
