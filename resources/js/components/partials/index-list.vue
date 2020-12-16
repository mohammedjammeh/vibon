<template>
    <ul>
        <li v-for="vibe in filteredVibes">
            <router-link :to="{ name: 'showVibe', params: { id: vibe.id }}" :class="isShowing(vibe)">
                <span v-if="isPlaying(vibe)">
                    {{ vibe.name }} - playing
                    {{ requestsCount(vibe) }}
                </span>
                <span v-else>
                    {{ vibe.name }}
                    {{ requestsCount(vibe) }}
                </span>
            </router-link>
            <br>
        </li>
    </ul>
</template>

<script>
    import vibes from '../../core/vibes';

    export default {
        props: ['filteredVibes'],

        data() {
            return {
            vibes: vibes
        }
    },

        methods: {
            isShowing: function (vibe) {
                return vibe.id === this.vibes.showID ? 'showing' : '';
            },

            isPlaying: function (vibe) {
                return vibe.id === this.vibes.playingID;
            },

            requestsCount: function (vibe) {
                if(vibe.join_requests.length > 0 && vibe.destroyable) {
                    return '(' + vibe.join_requests.length + ')';
                }
            }
        },
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