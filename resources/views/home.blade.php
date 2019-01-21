@extends('layouts.app')

@section('title', 'Vibon Home')

@section('content')

    <div class="container">

        <a href="{{ route('vibe.create') }}">Start a vibe</a>

        <br><br>





        @if(session('message'))

            <p>{{ session('message') }}</p>

        @endif





        @if(count($responseNotifications) > 0)

            <h3>Notifications</h3>

            @foreach($responseNotifications as $responseNotification)

                @foreach($vibes as $vibe)

                    @if($vibe->id == $responseNotification->data['vibe_id'])

                        @if($responseNotification->data['response'] == 1)

                            <p>Your request to join '{{ $vibe->title }}' has been accepted.</p>

                        @elseif($responseNotification->data['response'] == 0)

                            <p>Sorry, your request to join '{{ $vibe->title }}' has been rejected.</p>

                        @endif

                    @endif

                @endforeach

            @endforeach

        @endif

        <br>
        



    
        @if(!empty($userVibesTracks))

            <h3>My Vibes</h3>

            @foreach($userVibesTracks['vibes'] as $vibe)

                @if($vibe->pivot->owner == 1)

                    <a href="{{ route('vibe.show', ['id' => $vibe->id]) }}">

                        {{ $vibe->title }}

                            @for($i = 0; $i < count($requestNotifications); $i++)

                                @if($requestNotifications[$i]->data['vibe_id'] == $vibe->id)
                                   
                                    @if($i == count($requestNotifications) - 1)

                                        ({{ count($requestNotifications) }})
                                        
                                    @endif
                                        
                                @endif

                            @endfor

                    </a>

                    <br>

                @endif

            @endforeach

            <br><br><br>




            <h3>Random Tracks</h3>

            @foreach($trackRecommendations as $trackRecommendation)

                <img src="{{ $trackRecommendation->album->images[0]->url }}">

                <p>{{ $trackRecommendation->name }}</p>


                @foreach($userVibesTracks['vibes'] as $userVibe)

                    @if(in_array($userVibe->id, $trackRecommendation->belongs_to_user_vibes))

                        <form method="POST" action="{{ route('trackvibe.destroy', ['vibe' => $userVibe->id, 'track' => $trackRecommendation->vibon_id]) }}">

                            @csrf

                            @method('DELETE')

                            <input type="submit" name="track-vibe-store" value="{{ $userVibe->title }}" style="background:red;">

                        </form>

                        <br>

                    @else 

                        <form method="POST" action="{{ route('trackvibe.store') }}">

                            @csrf

                            <input type="hidden" name="track-api-id" value="{{ $trackRecommendation->id }}">

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
