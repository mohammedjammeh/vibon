@extends('layouts.app')

@section('title', 'Start a vibe')

@section('content')

    <div class="container">

        <p>Start a vibe</p>

        <form method="POST" action="{{ route('vibe.store') }}">

            @csrf

            @if($errors->any())

                <div>

                    @foreach($errors->all() as $error)

                        <p>{{ $error }}</p>

                    @endforeach

                </div>

            @endif




            <div>

                <input type="text" name="name" placeholder="Name" class="{{ $errors->has('title') ? 'error' : '' }}" value="{{ old('name') }}">
                
            </div>

            <br>



             <div>

                <label>Open</label>

                <select name="open" class="{{ $errors->has('open') ? 'error' : '' }}">

                    <option value="0">No</option>

                    <option value="1">Yes</option>

                </select>

            </div>

            <br>





            <div>

                <label>Auto DJ</label>

                <select name="auto_dj" class="{{ $errors->has('auto_dj') ? 'error' : '' }}">

                    <option value="0">No</option>

                    <option value="1">Yes</option>

                </select>

            </div>

            <br>





            <div>

                <textarea name="description" cols="19" rows="5" placeholder="Description" class="{{ $errors->has('description') ? 'error' : '' }}">{{ old('description') }}</textarea>

            </div>

            <br>





            <div>

                <input type="submit" name="vibe-create" value="Start">

            </div>

        </form>

    </div>

@endsection
