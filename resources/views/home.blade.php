@extends('layouts.app')

@section('title', 'Vibon Home')

@section('content')

    <div class="container">

        <a href="{{ route('vibe.create') }}">Start a vibe</a>

        <br><br>





        @if(session('message'))

            <p>{{ session('message') }}</p>

        @endif





        @if(count($user->notifications) > 0)

            <h3>Notifications</h3>

            @foreach($user->notifications as $notification)

                @foreach($vibes as $vibe)

                    @if($vibe->id == $notification->data['vibe_id'])

                        @if($notification->type == 'App\Notifications\ResponseToJoinAVibe')

                            @if($notification->data['response'] == 1)

                                <p>Your request to join '{{ $vibe->title }}' has been accepted.</p>

                            @elseif($notification->data['response'] == 0)

                                <p>Sorry, your request to join '{{ $vibe->title }}' has been rejected.</p>

                            @endif

                        @elseif($notification->type == 'App\notification\RemovedFromAVibe')

                            <p>You have been removed from the '{{ $vibe->title }}' vibe.</p>

                        @endif

                    @endif

                @endforeach

            @endforeach

        @endif

        <br>
        



    
        @if(!empty($user))

            <h3>My Vibes</h3>

            @foreach($user['vibes'] as $vibe)

                @if($vibe->pivot->owner == 1)

                    <a href="{{ route('vibe.show', ['id' => $vibe->id]) }}">

                        {{ $vibe->title }}

                        @for($notification = 0; $notification < count($user->requestNotifications()); $notification++)

                            @if($user->requestNotifications()[$notification]->data['vibe_id'] == $vibe->id)
                               
                                @if($notification == count($user->requestNotifications()) - 1)

                                    ({{ count($user->requestNotifications()) }})
                                    
                                @endif
                                    
                            @endif

                        @endfor

                    </a>

                    <br>

                @endif

            @endforeach

            <br><br><br>




            <h3>Random Tracks</h3>

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



                <br><br><br>

            @endforeach


        @endif

    </div>

@endsection
