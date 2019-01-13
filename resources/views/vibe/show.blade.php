@extends('layouts.app')

@section('title', 'Start a vibe')

@section('content')
    <div class="container">
        @if (session('message'))
            <p>{{ session('message') }}</p>
        @endif

        <p>{{ $showContent['vibe']->title }}</p>
        <p>{{ $showContent['vibe']->description }}</p>

        @can('member', $showContent['vibe'])
            <a href="/vibe/{{ $showContent['vibe']->id }}/edit">Edit</a>
        @endcan

        @can('owner', $showContent['vibe'])
            <form method="POST" action="/vibe/{{ $showContent['vibe']->id }}">
                @csrf
                @method('DELETE')

                <div>
                    <input type="submit" name="delete-vibe" value="Delete">
                </div>
            </form>
        @endcan


        <a href="/uservibe/{{ $showContent['vibe']->id }}">Join</a>

        <br><br><br>

        <h3>Members</h3>
        @foreach($showContent['members'] as $member)
            <p>{{ $member->name }}</p>

            @if($member->pivot->owner == 0)
                @can('owner', $showContent['vibe'])
                    <form method="POST" action="/uservibe/vibe/{{ $member->pivot->vibe_id }}/user/{{ $member->id }}">
                        @csrf
                        @method('DELETE')

                        <input type="submit" name="vibe-member-delete" value="Remove">
                    </form>
                @endcan

            @else 
                <p>Vibe Admin</p>
            @endif

            <br><br>
        @endforeach

        <br><br><br>

        <h3>Tracks</h3>
        @foreach($showContent['tracks'] as $thisVibeTrack)
            <img src="{{ $thisVibeTrack->album->images[0]->url }}">
            <p>{{ $thisVibeTrack->name }}</p>

            @foreach($showContent['user'][0]['vibes'] as $vibe)
                <form method="POST" action="/trackvibe">
                    @csrf

                    <input type="hidden" name="track-api-id" value="{{ $thisVibeTrack->id }}">

                    <input type="hidden" name="vibe-api-id" value="{{ $vibe->api_id }}">

                    <input type="hidden" name="vibe-id" value="{{ $vibe->id }}">

                    <input type="submit" name="track-vibe-submit" value="{{ $vibe->title }}" style="
                        @for($track = 0; $track < count($vibe->tracks); $track++)
                            @if($thisVibeTrack->id == $vibe->tracks[$track]->api_id)
                                background:red;
                                @break
                            @endif
                        @endfor
                    ">
                </form>

                @for($track = 0; $track < count($vibe->tracks); $track++)
                    @if($thisVibeTrack->id == $vibe->tracks[$track]->api_id)
                        <form method="POST" action="/trackvibe/vibe/{{ $vibe->tracks[$track]->pivot->vibe_id }}/track/{{ $vibe->tracks[$track]->pivot->track_id }}">
                            @csrf
                            @method('DELETE')

                            <input type="submit" name="track-vibe-delete" value="Remove">
                        </form>
                        @break
                    @endif
                @endfor

                <br>
            @endforeach

            <br><br><br><br><br>
        @endforeach
    </div>
@endsection
