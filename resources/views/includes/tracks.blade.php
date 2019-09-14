            <div class="api-tracks">
                @foreach($apiTracks as $apiTrack)
                    <div class="playback-play-track">
                        <a href="#">
                            <img src="{{ $apiTrack->album->images[0]->url }}">

                            <span class="track-api-id" hidden>{{ $apiTrack->id }}</span>
                            <span class="track-vibon-id" hidden>{{ $apiTrack->vibon_id }}</span>
                            <span class="track-uri" hidden>{{ $apiTrack->uri }}</span>
                        </a>
                        <p style="white-space: nowrap; overflow: hidden;">{{ $apiTrack->name }}</p>

                        @if(!$vibe->auto_dj)
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

                            @if($apiTrack->is_voted_by_user)
                                <form method="POST" action="{{ route('vote.destroy', ['vibe' => $userVibe->id, 'track' => $apiTrack->vibon_id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="submit" name="vote-destroy" value="Unvote" style="background:red;">
                                    {{ $apiTrack->votes_count }}
                                </form>
                                <br>
                            @else
                                <form method="POST" action="{{ route('vote.store', ['vibe' => $userVibe->id, 'track' => $apiTrack->vibon_id]) }}">
                                    @csrf
                                    <input type="submit" name="vote-store" value="Vote">
                                    {{ $apiTrack->votes_count }}
                                </form>
                                <br>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>



