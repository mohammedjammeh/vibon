@extends('layouts.app')

@section('title', 'Edit a vibe')

@section('content')

    <div class="container">

        <p>Edit a vibe</p>

        <form method="POST" action="{{ route('vibe.update', ['id' => $vibe->id]) }}">

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

            <br>




            <div>
                <label>Type</label>

                <select name="type" class="{{ $errors->has('type') ? 'error' : '' }}">

                    @if($vibe->privateType())

                        <option value="1" selected>Private</option>

                        <option value="2">Public</option>

                    @else 

                        <option value="1">Private</option>

                        <option value="2" selected>Public</option>

                    @endif

                </select>

            </div>

            <br>





            <div>

                <label>Auto DJ</label>

                <select name="auto_dj" class="{{ $errors->has('auto_dj') ? 'error' : '' }}">

                    @if($vibe->autoDJ())

                        <option value="0">No</option>

                        <option value="1" selected>Yes</option>

                    @else 

                        <option value="0" selected>No</option>

                        <option value="1">Yes</option>

                    @endif

                </select>

            </div>

            <br>





            <div>

                <textarea name="description" cols="19" rows="5" placeholder="Description" class="{{ $errors->has('description') ? 'error' : '' }}">{{ $vibe->description }}</textarea>

            </div>

            <br>




            <div>
                <input type="submit" name="vibe-update" value="Update">
            </div>

        </form>

    </div>

@endsection
