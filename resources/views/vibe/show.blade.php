@extends('layouts.app')

@section('title', 'Start a vibe')

@section('content')
    <div class="container">
        @if (session('message'))
            <p>{{ session('message') }}</p>
        @endif

        <p>{{ $vibe->title }}</p>
        <p>{{ $vibe->description }}</p>
        <p>{{ $vibe->key }}</p>

        @can('update', $vibe)
            <a href="/vibe/{{ $vibe->id }}/edit">Edit</a>
        @endcan

        @can('delete', $vibe)
            <form method="POST" action="/vibe/{{ $vibe->id }}">
                @csrf
                @method('DELETE')

                <div>
                    <input type="submit" name="delete-vibe" value="Delete">
                </div>
            </form>
        @endcan
    </div>
@endsection
