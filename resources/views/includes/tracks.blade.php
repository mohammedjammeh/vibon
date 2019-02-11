            @foreach($apiTracks as $apiTrack)
                <img src="{{ $apiTrack->album->images[0]->url }}">
                <p>{{ $apiTrack->name }}</p>

                @foreach($user['vibes'] as $userVibe)
                    @if(in_array($userVibe->id, $apiTrack->vibes))
                        <form method="POST" action="{{ route('track-vibe.destroy', ['vibe' => $userVibe->id, 'track' => $apiTrack->vibon_id]) }}">
                            @csrf
                            @method('DELETE')
                            <input type="submit" name="track-vibe-store" value="{{ $userVibe->name }}" style="background:red;">
                        </form>
                        <br>
                    @else 
                        <form method="POST" action="{{ route('track-vibe.store', ['vibe' => $userVibe->id]) }}">
                            @csrf
                            <input type="hidden" name="track-api-id" value="{{ $apiTrack->id }}">
                            <input type="submit" name="track-vibe-store" value="{{ $userVibe->name }}">
                        </form>
                        <br>
                    @endif
                @endforeach
                <br>
            @endforeach



