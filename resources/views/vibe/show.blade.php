@extends('layouts.app')
@section('title', $vibe->name)
@section('content')
    <div class="container">
        @if (session('message'))
            <p>{{ session('message') }}</p>
        @endif

        <p>{!! $vibe->name !!}</p>
        <p>{!! $vibe->description !!}</p>

        @if($vibe->open)
            <p>Opened</p>
        @else
            <p>Not Opened</p>
        @endif

        @if($vibe->auto_dj)
            <p>Auto DJ</p>
        @else
            <p>Manual DJ</p>
        @endif

        @can('update', $vibe)
            <a href="{{ route('vibe.edit', ['id' => $vibe->id]) }}">Edit</a>
            <br><br>
        @endcan

        @can('delete', $vibe)
            @if($vibe->auto_dj) 
                <form method="POST" action="{{ route('track-vibe-auto.update', ['vibe' => $vibe->id]) }}">
                    @csrf
                    <input type="submit" name="vibe-tracks-update" value="Refresh">
                </form>
                <br>
            @endif
            
            <form method="POST" action="{{ route('vibe.destroy', ['id' => $vibe->id]) }}">
                @csrf
                @method('DELETE')
                <div>
                    <input type="submit" name="vibe-delete" value="Delete">
                </div>
            </form>
            <br><br>

            @if($vibe->synced)
                <p>All Synced</p>
            @else
                <p>Sync using one of the two:</p>
                <form method="POST" action="{{ route('vibe.sync', ['vibe' => $vibe]) }}">
                    @csrf
                    <input type="submit" name="vibe" value="Vibe"><br><br>
                    <input type="submit" name="playlist" value="Playlist">
                </form>
            @endif
            <br><br>

            <div class="playback-buttons" style="display: none">
                <div class="playback-resume">
                    <a href="#">Play</a>
                    <br><br>
                </div>
                
                <div class="playback-pause"  style="display: none;">
                    <a href="#">Pause</a>
                    <br><br>
                </div>
                
                <div class="playback-previous">
                    <a href="#">Previous</a>
                    <br><br>
                </div>
                
                <div class="playback-next">
                    <a href="#">Next</a>
                    <br><br>
                </div>
            </div>
            <br>

            @if(count($vibe->joinRequests) > 0)
                <h3>Requests</h3>
                @foreach($vibe->joinRequests as $joinRequest)
                    <p>{{ $joinRequest->user->username }}</p>
                    <form method="POST" action="{{ route('join-request.respond', ['joinRequest' => $joinRequest]) }}">
                        @csrf
                        @method('DELETE')
                        <input type="submit" name="accept" value="Accept">
                        <input type="submit" name="reject" value="Reject">
                    </form>
                    <br><br>
                @endforeach
            @endif
        @endcan

        @cannot('delete', $vibe)
            @if($vibe->hasMember(auth()->user())) 
                <form method="POST" action="{{ route('user-vibe.destroy', ['vibe' => $vibe->id, 'user' => $user->id]) }}">
                    @csrf
                    @method('DELETE')
                    <input type="submit" name="vibe-leave" value="Leave Vibe">
                </form>
            @elseif($vibe->hasJoinRequestFrom(auth()->user()))
                <form method="POST" action="{{ route('join-request.destroy', ['joinRequest' => $vibe->joinRequestFrom(auth()->user())]) }}">
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
            <p>{{ $member->username }}</p>
            @if(!$member->pivot->owner)
                @can('delete', $vibe)
                    <form method="POST" action="{{ route('user-vibe.destroy', ['vibe' => $member->pivot->vibe_id, 'user' => $member->id]) }}">
                        @csrf
                        @method('DELETE')
                        <input type="submit" name="vibe-member-remove" value="Remove">
                    </form>
                @endcan
            @else 
                <p>Admin</p>
            @endif
            <br><br>
        @endforeach
        <br><br><br>

        @if(count($apiTracks) > 0)
            <h3>Tracks</h3>
                <span class="vibe-uri" hidden>{{ $vibe->uri }}</span>
            @include('includes.tracks')
        @endif
    </div>
@endsection
