@extends('layouts.app')

@section('title', 'Start a vibe')

@section('content')

    <div class="container">



        @if (session('message'))

            <p>{{ session('message') }}</p>

        @endif




        <p>{{ $vibe->title }}</p>

        <p>{{ $vibe->description }}</p>




        <p>

            @if($vibe->type == 1)
                <p>Private Account</p>
            @else
                <p>Public Account</p>
            @endif

        </p>




        <p>
            
            @if($vibe->auto_dj == 0)
                <p>Manual DJ</p>
            @else
                <p>Auto DJ</p>
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


            @if(count($vibe->joinRequesters()) > 0)

                <h3>Requests</h3>

                @foreach($vibe->joinRequesters() as $joinRequester)

                    <p>{{ $joinRequester->name }}</p>

                    <form method="POST" action="{{ route('join-request.respond', ['vibe' => $vibe->id, 'user' => $joinRequester->id]) }}">

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

            @if($vibe->userIsAMember()) 

                <form method="POST" action="{{ route('uservibe.destroy', ['vibe' => $vibe->id, 'user' => $user->id]) }}">

                    @csrf

                    @method('DELETE')

                    <input type="submit" name="vibe-leave" value="Leave Vibe">

                </form>

            @elseif($vibe->userSentAJoinRequest())

                <form method="POST" action="{{ route('join-request.cancel', ['vibe' => $vibe->id]) }}">

                    @csrf

                    @method('DELETE')

                    <input type="submit" name="vibe-join-cancel" value="Cancel Join Request">

                </form>

            @else

                @if($vibe->privateType())

                    <form method="POST" action="{{ route('join-request.join', ['vibe' => $vibe->id]) }}">

                        @csrf

                        <input type="submit" name="vibe-join" value="Join Vibe">

                    </form>

                @else 

                    <form method="POST" action="{{ route('uservibe.store', ['vibe' => $vibe->id]) }}">

                        @csrf

                        <input type="submit" name="vibe-join" value="Join Vibe">

                    </form>

                @endif

            @endif


             <br><br><br>
             
        @endcan








        <h3>Members</h3>

        @foreach($vibe->members() as $member)

            <p>{{ $member->name }}</p>

            @if($member->pivot->owner == 0)

                @can('delete', $vibe)

                    <form method="POST" action="{{ route('uservibe.destroy', ['vibe' => $member->pivot->vibe_id, 'user' => $member->id]) }}">

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

            @foreach($apiTracks as $apiTrack)

                <img src="{{ $apiTrack->album->images[0]->url }}">

                <p>{{ $apiTrack->name }}</p>



                @foreach($user['vibes'] as $userVibe)

                    @if(in_array($userVibe->id, $apiTrack->vibes))

                        <form method="POST" action="{{ route('trackvibe.destroy', ['vibe' => $userVibe->id, 'track' => $apiTrack->vibon_id]) }}">

                            @csrf

                            @method('DELETE')

                            <input type="submit" name="track-vibe-store" value="{{ $userVibe->title }}" style="background:red;">

                        </form>

                        <br>

                    @else 

                        <form method="POST" action="{{ route('trackvibe.store') }}">

                            @csrf

                            <input type="hidden" name="track-api-id" value="{{ $apiTrack->id }}">

                            <input type="hidden" name="vibe-api-id" value="{{ $userVibe->api_id }}">

                            <input type="hidden" name="vibe-id" value="{{ $userVibe->id }}">

                            <input type="submit" name="track-vibe-store" value="{{ $userVibe->title }}">

                        </form>

                        <br>

                    @endif

                @endforeach

            @endforeach


            <br><br><br>

        @endif





    </div>
    
@endsection
