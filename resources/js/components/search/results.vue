<template>
    <div>
        <h4>Search Results..</h4>
        <div class="api-tracks" v-if="tracksAndVibesAreloaded()">
            <div v-for="track in this.search.tracks">
                <search-track :track="track" :search-tracks="search.tracks"></search-track>
            </div>
        </div>
        <div v-else>
            <br><br>
            <p>loading..</p>
        </div>
    </div>
</template>

<script>
    import search from '../../core/search';
    import vibes from '../../core/vibes';
    import user from '../../core/user';
    import playback from '../../core/playback.js';
    import Form from '../../classes/Form';
    import track from '../track/search-track.vue';

    export default {
        components: {
            'search-track': track
        },

        data() {
            return {
                vibes: vibes,
                user: user,
                playback: playback,
                search: search,
                input: this.$route.params.input,
                // tracks: {},
                removeTrackForm: new Form({}),
                addTrackForm: new Form({}),
            }
        },

        created() {
            this.search.searchInput(this.input);
        },

        methods: {
            tracksAndVibesAreloaded() {
                return Object.keys(this.search.tracks).length > 0 && Object.keys(this.vibes.all).length > 0;
            }
        }
    }
</script>