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
        window.onSpotifyWebPlaybackSDKReady = () => {
            // AUTO GET USER TOKEN
            const player = new Spotify.Player({
                name: 'Vibon',
                getOAuthToken: cb => { cb(
                    $.ajax({
                        type: 'GET',
                        url: '/user/access-token',
                        data: {},
                        success: function() {}
                    })
                ); }
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

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    if (state['position'] === 0) {
                        $('.api-tracks > div').removeAttr('style');
                        if(newPlayingTrackIsOnThisPage($trackSpan)) {
                            $trackSpan.parent().parent().attr('style', 'background: green;');

                            $.ajax({
                                type: 'PUT',
                                url: '/vibe-playback/vibe/' + $vibeVibonID + '/track/' + $trackVibonID,
                                data: {},
                                success: function(data) {
                                    console.log(data);
                                }
                            });
                        }
                    }

                    function newPlayingTrackIsOnThisPage($trackSpan) {
                        if($trackSpan.length > 0) {
                            return true;
                        }
                        return false;
                    }

                    if(state['paused'] === true) {
                        $('.playback-play').show();
                        $('.playback-pause').hide();
                    } else {
                        $('.playback-play').hide();
                        $('.playback-pause').show();
                    }
                }
            });



            // Ready
            player.addListener('ready', ({ device_id }) => {
                console.log('Ready with Device ID', device_id);

                function trackAjax($type, $url, $text, $position) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: $type,
                        url: $url,
                        data: {
                            'device_id': device_id,
                            'position': $position
                        },
                        async: true,
                        success: function() {
                            console.log($text);
                        }
                    });
                }

                $(document).on('click', '.playback-play-track a', function(event) {
                    event.preventDefault();

                    $vibeURI = $('.vibe-uri').text();
                    $trackURI = $(this).children('span.track-uri').text();

                    $URL = '/playback/vibe/' + $vibeURI + '/track/' + $trackURI + '/play';
                    trackAjax('PUT', $URL, 'Playing track.', 0);
                });

                $(document).on('click', '.playback-play a', function(event) {
                    event.preventDefault();

                    $vibeURI = $('.vibe-uri').text();
                    $trackURI = $('.api-tracks div[style="background: green;"] span.track-uri').text();


                    $.ajax({
                        type: 'GET',
                        url: '/playback/currently-playing',
                        data: {},
                        success: function(data) {
                            if(data['item']) {
                                $URL = '/playback/resume';
                                trackAjax('PUT', $URL, 'Resume track.', data['progress_ms']);
                            } else {
                                $URL = '/playback/vibe/' + $vibeURI + '/track/' + $trackURI + '/play';
                                trackAjax('PUT', $URL, 'Playing track.', 0);
                            }
                        }
                    });
                });

                $(document).on('click', '.playback-pause a', function(event) {
                    event.preventDefault();
                    trackAjax('PUT', '/playback/pause', 'Paused track.', 0);
                });

                $(document).on('click', '.playback-previous a', function(event){
                    event.preventDefault();
                    trackAjax('POST', '/playback/previous', 'Playing previous track.', 0);
                });

                $(document).on('click', '.playback-next a', function(event){
                    event.preventDefault();
                    trackAjax('POST', '/playback/next', 'Playing next track.', 0);
                });
            });


            // Not Ready
            player.addListener('not_ready', ({ device_id }) => {
                console.log('Device ID has gone offline', device_id);
            });


            // Connect to the player!
            player.connect();
        };
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
