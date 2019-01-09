@extends('layouts.app')

@section('title', 'Edit a vibe')

@section('content')
    <div class="container">
        <p>Edit a vibe</p>
        <form method="POST" action="/vibe/{{ $vibe->id }}">
            @csrf
            @method('PATCH')

            @if($errors->any())
                <div>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <input type="text" name="title" placeholder="Title" class="{{ $errors->has('title') ? 'error' : '' }}" value="{{ $vibe->title }}">
            </div>

            <div>
                <textarea name="description" cols="19" rows="5" placeholder="Description" class="{{ $errors->has('description') ? 'error' : '' }}">{{ $vibe->description }}</textarea>
            </div>

            <div>
                <input type="submit" name="update-vibe" value="Update">
            </div>
        </form>
    </div>
@endsection
