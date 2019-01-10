@extends('layouts.app')

@section('title', 'Start a vibe')

@section('content')
    <div class="container">
        @if (session('message'))
            <p>{{ session('message') }}</p>
        @endif

        <p>{{ $vibeToShow['vibe']->title }}</p>
        <p>{{ $vibeToShow['vibe']->description }}</p>
        <p>{{ $vibeToShow['vibe']->key }}</p>

        @can('update', $vibeToShow['vibe'])
            <a href="/vibe/{{ $vibeToShow['vibe']->id }}/edit">Edit</a>
        @endcan

        @can('delete', $vibeToShow['vibe'])
            <form method="POST" action="/vibe/{{ $vibeToShow['vibe']->id }}">
                @csrf
                @method('DELETE')

                <div>
                    <input type="submit" name="delete-vibe" value="Delete">
                </div>
            </form>
        @endcan
    </div>
@endsection
