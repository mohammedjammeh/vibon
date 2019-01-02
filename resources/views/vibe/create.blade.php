@extends('layouts.app')

@section('title', 'Start a vibe')

@section('content')
    <div class="container">
        <p>Start a vibe</p>
        <form method="POST" action="/vibe">
            @csrf
            <div>
                @foreach($errors->all as $error)
                    <li>$error</li>
                @endforeach
            </div>
            <div>
                <input type="text" name="title" placeholder="Title">
            </div>

            <div>
                <textarea name="description" cols="19" rows="5" placeholder="Description"></textarea>
            </div>
            <div>
                <input type="submit" name="vibe-submit" value="Start">
            </div>
        </form>
    </div>
@endsection
