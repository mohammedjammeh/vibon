<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Vibon')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">Vibon</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('authorise') }}">Spotify</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('authorise') }}">Apple</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->username }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://sdk.scdn.co/spotify-player.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            window.onSpotifyWebPlaybackSDKReady = () => {
                function getAccessToken() {
                    var now = new Date();
                    now.setHours(now.getHours() - 1);
                    var oneHourAgo = now.getTime();

                    if(localStorage['token_set_at'] >= oneHourAgo) {
                        return localStorage['access_token'];
                    } else {
                        var user = $.ajax({
                            type: 'GET',
                            dataType: 'json',
                            async: false,
                            url: '/user',
                            success: function(data) {
                                return data;
                            }
                        });

                        var userAttributes = JSON.parse(user.responseText);
                        localStorage['token_set_at'] = new Date(userAttributes['token_set_at']).getTime();
                        localStorage['access_token'] = userAttributes['access_token'];
                        return localStorage['access_token'];
                    }
                }


                const player = new Spotify.Player({
                    name: 'Vibon',
                    getOAuthToken: cb => { cb(getAccessToken()); }
                });

                const play = ({
                    vibe_uri,
                    track_uri,
                    playerInstance: {
                        _options: {
                          getOAuthToken,
                          id
                        }
                }}) => {
                    getOAuthToken(access_token => {
                        fetch(`https://api.spotify.com/v1/me/player/play?device_id=${id}`, {
                            method: 'PUT',
                            body: JSON.stringify({
                                context_uri: vibe_uri,
                                offset: {
                                    uri: track_uri
                                },
                            }),
                            headers: {
                                'Content-Type': 'application/json',
                                'Authorization': `Bearer ${access_token}`
                            },
                        });
                    });
                };

                // play track
                $(document).on('click', '.playback-play-track a', function(event) {
                    event.preventDefault();
                    let vibeURI = $('.vibe-uri').text();
                    let trackURI = $(this).children('span.track-uri').text();

                    play({
                        playerInstance: player,
                        vibe_uri: vibeURI,
                        track_uri: trackURI
                    });
                });

                // resume
                $(document).on('click', '.playback-resume a', function(event) {
                    event.preventDefault();

                    player.resume().then(() => {
                        console.log('Resumed!');
                    });
                });

                // pause
                $(document).on('click', '.playback-pause a', function(event) {
                    event.preventDefault();
                    player.pause().then(() => {
                        console.log('Paused!');
                    });
                });

                // previous track
                $(document).on('click', '.playback-previous a', function(event) {
                    event.preventDefault();
                    player.previousTrack().then(() => {
                        console.log('Set to previous track!');
                    });
                });

                // next track
                $(document).on('click', '.playback-next a', function(event) {
                    event.preventDefault();
                    player.nextTrack().then(() => {
                        console.log('Set to next track!');
                    });
                });





                // Error handling
                player.addListener('initialization_error', ({ message }) => { console.error(message); });
                player.addListener('account_error', ({ message }) => { console.error(message); });
                player.addListener('playback_error', ({ message }) => { console.error(message); });
                player.addListener('authentication_error', ({ message }) => { console.error(message); });



                // Playback status updates
                player.addListener('player_state_changed', state => {
                    if(state) {
                        $trackID = state['track_window']['current_track']['linked_from']['id'] ? state['track_window']['current_track']['linked_from']['id'] : state['track_window']['current_track']['id'];
                        $trackSpan = $('.playback-play-track a').children('span.track-api-id:contains(' + $trackID + ')');
                        $trackVibonID = $trackSpan.siblings('.track-vibon-id').text();
                        $vibeVibonID = $('.vibe-vibon-id').text();

                        $('.playback-buttons').show();
                        $('.api-tracks > div').removeAttr('style');
                        $trackSpan.parent().parent().attr('style', 'background: green;');

                        // $.ajaxSetup({
                        //     headers: {
                        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        //     }
                        // });
                        //
                        // if (state['position'] === 0 && newPlayingTrackIsOnThisPage($trackSpan)) {
                        //     $.ajax({
                        //         type: 'PUT',
                        //         url: '/vibe-playback/vibe/' + $vibeVibonID + '/track/' + $trackVibonID,
                        //         data: {},
                        //         success: function(data) {
                        //             console.log(data);
                        //         }
                        //     });
                        // }
                        checkAndUpdatePlayOrPauseButton();

                    } else {
                        $('.playback-buttons').hide();
                        $('.api-tracks > div').removeAttr('style');
                    }




                    function newPlayingTrackIsOnThisPage($trackSpan) {
                        if($trackSpan.length > 0) {
                            return true;
                        }
                        return false;
                    }

                    function checkAndUpdatePlayOrPauseButton() {
                        if(state['paused'] === true) {
                            $('.playback-resume').show();
                            $('.playback-pause').hide();
                        } else {
                            $('.playback-resume').hide();
                            $('.playback-pause').show();
                        }
                    }
                });



                // Ready
                player.addListener('ready', ({ device_id }) => {
                    console.log('Ready with Device ID', device_id);
                });


                // Not Ready
                player.addListener('not_ready', ({ device_id }) => {
                    console.log('Device ID has gone offline', device_id);
                });


                // Connect to the player!
                player.connect();
            };
        });
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
