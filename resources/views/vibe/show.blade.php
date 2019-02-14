@extends('layouts.app')
@section('title', 'Start a vibe')
@section('content')
    <div class="container">
        @if (session('message'))
            <p>{{ session('message') }}</p>
        @endif

        <p>{{ $vibe->name }}</p>
        <p>{{ $vibe->description }}</p>

        <p>
            @if($vibe->open)
                <p>Opened</p>
            @else
                <p>Not Opened</p>
            @endif
        </p>

        <p>
            @if($vibe->auto_dj)
                <p>Auto DJ</p>
            @else
                <p>Manual DJ</p>
            @endif
        </p>

        @can('update', $vibe)
            <a href="{{ route('vibe.edit', ['id' => $vibe->id]) }}">Edit</a>
            <br><br>
        @endcan

        @can('delete', $vibe)
            <form method="POST" action="{{ route('vibe.destroy', ['id' => $vibe->id]) }}">
                @csrf
                @method('DELETE')
                <div>
                    <input type="submit" name="vibe-delete" value="Delete">
                </div>
            </form>
            <br><br>

            @if(count($vibe->joinRequests) > 0)
                <h3>Requests</h3>
                @foreach($vibe->joinRequests as $joinRequest)
                    <p>{{ $joinRequest->user->name }}</p>
                    <form method="POST" action="{{ route('join-request.respond', ['joinRequest' => $joinRequest->id, 'vibe' => $vibe->id]) }}">
                        @csrf
                        @method('PATCH')
                        <input type="submit" name="accept" value="Accept">
                        <input type="submit" name="reject" value="Reject">
                    </form>
                    <br><br>
                @endforeach
            @endif
        @endcan

        @cannot('delete', $vibe)
            @if($vibe->hasMember(auth()->user()->id)) 
                <form method="POST" action="{{ route('user-vibe.destroy', ['vibe' => $vibe->id, 'user' => $user->id]) }}">
                    @csrf
                    @method('DELETE')
                    <input type="submit" name="vibe-leave" value="Leave Vibe">
                </form>
            @elseif($vibe->hasJoinRequestFrom(auth()->user()->id))
                <form method="POST" action="{{ route('join-request.destroy', ['joinRequest' => $vibe->hasJoinRequestFrom(auth()->user()->id), 'vibe' => $vibe->id]) }}">
                    @csrf
                    @method('DELETE')
                    <input type="submit" name="vibe-join-destroy" value="Cancel Join Request">
                </form>
            @else
                @if($vibe->open)
                    <form method="POST" action="{{ route('user-vibe.store', ['vibe' => $vibe->id]) }}">
                        @csrf
                        <input type="submit" name="vibe-store" value="Join Vibe">
                    </form>
                @else 
                    <form method="POST" action="{{ route('join-request.store', ['vibe' => $vibe->id]) }}">
                        @csrf
                        <input type="submit" name="vibe-store" value="Send Join Request">
                    </form>
                @endif
            @endif
             <br><br><br>
        @endcan

        <h3>Members</h3>
        @foreach($vibe->users as $member)
            <p>{{ $member->name }}</p>
            @if($member->pivot->owner == 0)
                @can('delete', $vibe)
                    <form method="POST" action="{{ route('user-vibe.destroy', ['vibe' => $member->pivot->vibe_id, 'user' => $member->id]) }}">
                        @csrf
                        @method('DELETE')
                        <input type="submit" name="vibe-member-remove" value="Remove">
                    </form>
                @endcan
            @else 
                <p>Vibe Admin</p>
            @endif
            <br><br>
        @endforeach
        <br><br><br>

        @if(count($apiTracks) > 0) 
            <h3>Tracks</h3>
            @include('includes.tracks')
        @endif
    </div>
@endsection
