<template>
    <ul>
        <li v-for="vibe in filteredVibes">
            <router-link
                :to="{ name: 'showVibe', params: { id: vibe.id }}"
                :class="isShowing(vibe) ? 'showing' : ''"
            >
                <span>{{ vibe.name }}</span>
                <span v-if="isPlaying(vibe)"> - playing</span>
                <span v-if="requestsCountDisplayable(vibe)">({{ requestsCount(vibe) }})</span>
            </router-link>
            <br>
        </li>
    </ul>
</template>

<script>
    import vibes from '../../../../core/vibes';

    export default {
        props: ['filteredVibes'],

        data() {
            return {
                vibes: vibes
            }
        },

        methods: {
            isShowing(vibe) {
                return vibe.id === this.vibes.showID;
            },

            isPlaying(vibe) {
                return vibe.id === this.vibes.playingID;
            },

            requestsCount(vibe) {
                return vibe.join_requests.length + vibe.api_tracks.pending_to_attach.length + vibe.api_tracks.pending_to_detach.length;
            },

            requestsCountDisplayable(vibe) {
                return vibe.destroyable && this.requestsCount(vibe) > 0;
            }
        }
    }
</script>

<style scoped>
    .showing {
        color: #800000;
    }

    ul {
        padding: 0;
        margin: 0;
        list-style-type: none;
    }
</style>