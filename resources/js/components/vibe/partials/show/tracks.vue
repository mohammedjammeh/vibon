<template>
    <div>
        <div v-if="this.playlistHasTracks()">
            <h4>Playlist</h4>
            <div class="api-tracks">
                <div v-for="track in vibes.show.api_tracks.playlist">
                    <vibe-track :track="track" :playlistTrack="true"></vibe-track>
                </div>
            </div>
            <br><br>
        </div>

        <div v-if="this.playlistHasTracksNotOnVibe()">
            <h4>Playlist tracks not on vibe</h4>
            <div class="api-tracks">
                <div v-for="track in vibes.show.api_tracks.not_on_vibon">
                    <vibe-track :track="track" :playlistTrack="true"></vibe-track>
                </div>
            </div>
            <br><br>
        </div>

        <div v-if="this.vibeHasTracksNotOnPlaylist()">
            <h4>Vibe tracks not on playlist</h4>
            <div class="api-tracks">
                <div v-for="track in vibes.show.api_tracks.not_on_playlist">
                    <vibe-track :track="track" :playlistTrack="false"></vibe-track>
                </div>
            </div>
            <br><br>
        </div>

        <div v-if="this.vibeHasPendingTracks()">
            <h4>Pending tracks</h4>
            <div class="api-tracks">
                <div v-for="track in vibes.show.api_tracks.pending">
                    <vibe-track :track="track" :playlistTrack="false"></vibe-track>
                </div>
            </div>
            <br><br>
        </div>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes.js';
    import track from '../../../track/vibe-track.vue';

    export default {
        components: {
            'vibe-track': track,
        },

        data() {
            return {
                vibes: vibes,
                editMode: false,
            }
        },

        methods: {
            playlistHasTracks() {
                return Object.keys(this.vibes.show.api_tracks.playlist).length > 0;
            },

            vibeHasPendingTracks() {
                return Object.keys(this.vibes.show.api_tracks.pending).length > 0;
            },

            vibeHasTracksNotOnPlaylist() {
                return Object.keys(this.vibes.show.api_tracks.not_on_playlist).length > 0;
            },

            playlistHasTracksNotOnVibe() {
                return Object.keys(this.vibes.show.api_tracks.not_on_vibon).length > 0;
            },
        }
    }
</script>
