@extends('layouts.app')

@section('title', 'Vibon Home')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                        <a href="/vibe/create">Start a vibe</a>
                        <br>
                        <a href="#">Join a vibe</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
