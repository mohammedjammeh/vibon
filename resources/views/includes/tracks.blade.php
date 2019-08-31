            <div class="api-tracks">
                @foreach($apiTracks as $apiTrack)
                    <div class="playback-play-track">
                        <a href="#">
                            <img src="{{ $apiTrack->album->images[0]->url }}">

                            <span class="track-api-id" hidden>{{ $apiTrack->id }}</span>
                            <span class="track-vibon-id" hidden>{{ $apiTrack->vibon_id }}</span>
                            <span class="track-uri" hidden>{{ $apiTrack->uri }}</span>
                        </a>
                        <p>{{ $apiTrack->name }}</p>

                        @foreach($user['vibes'] as $userVibe)
                            @if(in_array($userVibe->id, $apiTrack->vibes))
                                <form method="POST" action="{{ route('track-vibe.destroy', ['vibe' => $userVibe->id, 'track' => $apiTrack->vibon_id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="submit" name="track-vibe-destroy" value="{{ $userVibe->name }}" style="background:red;">
                                </form>
                                <br>
                            @else
                                <form method="POST" action="{{ route('track-vibe.store', ['vibe' => $userVibe->id]) }}">
                                    @csrf
                                    <input type="hidden" name="track-api-id" value="{{ $apiTrack->id }}">
                                    <input type="submit" name="track-vibe-store" value="{{ $userVibe->name }}">
                                </form>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>



