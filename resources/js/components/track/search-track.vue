<template>
    <div :class="isPlaying">
        <play :track="track" :searchTracks="searchTracks"></play>

        <div>
            <h5>Manual Vibes</h5>
            <div v-for="userVibeID in user.manualVibesIDs">
                <remove-button :userVibeID="userVibeID" :trackID="track.vibon_id" :searchTracks="searchTracks" v-if="track.vibes.includes(userVibeID)"></remove-button>
                <add-button :userVibeID="userVibeID" :trackID="track.id" :searchTracks="searchTracks" v-else></add-button>
            </div>

            <h5>Auto Vibes</h5>
            <div v-for="userVibeID in user.autoVibesIDs">
                <remove-button :userVibeID="userVibeID" :trackID="track.vibon_id" :searchTracks="searchTracks" v-if="track.vibes.includes(userVibeID)"></remove-button>
                <add-button :userVibeID="userVibeID" :trackID="track.id" :searchTracks="searchTracks" v-else></add-button>
            </div>
        </div>
    </div>
</template>

<script>
    import user from '../../core/user.js';
    import vibes from '../../core/vibes.js';
    import search from '../../core/search.js';
    import play from './search/partials/play';
    import addButton from './search/buttons/add';
    import removeButton from './search/buttons/remove';

    export default {
        props: ['track', 'searchTracks'],

        components: {
            'play' : play,
            'add-button': addButton,
            'remove-button': removeButton
        },

        data() {
            return {
                search: search,
                user: user,
                vibes: vibes
            }
        },

        computed : {
            isPlaying() {
                if(this.search.playingTrack === this.track.id) {
                    return 'playback-play-track-search playing';
                }
                return 'playback-play-track-search';
            }
        }
    }
</script>

<style scoped>
    .playing {
        background: green;
    }
</style>