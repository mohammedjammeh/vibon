<template>
    <div>
        <div v-if="this.playlistHasTracks()">
            <h4>Playlist</h4>
            <div class="api-tracks">
                <div v-for="track in vibes.show.api_tracks.playlist">
                    <vibe-track :track="track" :type="'playlist'"></vibe-track>
                </div>
            </div>
            <br><br>
        </div>

        <div v-if="this.playlistHasTracksNotOnVibe()">
            <h4>Playlist tracks not on vibe</h4>
            <div class="api-tracks">
                <div v-for="track in vibes.show.api_tracks.not_on_vibon">
                    <vibe-track :track="track" :type="'not_on_vibon'"></vibe-track>
                </div>
            </div>
            <br><br>
        </div>

        <div v-if="this.vibeHasTracksNotOnPlaylist()">
            <h4>Vibe tracks not on playlist</h4>
            <div class="api-tracks">
                <div v-for="track in vibes.show.api_tracks.not_on_playlist">
                    <vibe-track :track="track" :type="'not_on_playlist'"></vibe-track>
                </div>
            </div>
            <br><br>
        </div>

        <div v-if="this.vibeHasPendingTracksToAttach()">
            <h4>Pending to attach</h4>
            <div class="api-tracks">
                <div v-for="track in vibes.show.api_tracks.pending_to_attach">
                    <vibe-track :track="track" :type="'pending_to_attach'"></vibe-track>
                </div>
            </div>
            <br><br>
        </div>

        <div v-if="this.vibeHasPendingTracksToDetach()">
            <h4>Pending to detach</h4>
            <div class="api-tracks">
                <div v-for="track in vibes.show.api_tracks.pending_to_detach">
                    <vibe-track :track="track" :type="'pending_to_detach'"></vibe-track>
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

            vibeHasPendingTracksToAttach() {
                return Object.keys(this.vibes.show.api_tracks.pending_to_attach).length > 0;
            },

            vibeHasPendingTracksToDetach() {
                return Object.keys(this.vibes.show.api_tracks.pending_to_detach).length > 0;
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
