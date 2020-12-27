<template>
    <div>
        <div v-if="this.vibes.readyToShow()">
            <div v-if="this.vibeHasMessageToShow()">
                <p v-text="this.vibes.message"></p>
                <br>
            </div>

            <show-edit></show-edit>

            <owner-buttons></owner-buttons>

            <join-requests></join-requests>

            <member-buttons></member-buttons>

            <user-notifications :notifications="this.vibes.show.notifications"></user-notifications>

            <members></members>


            <div v-if="this.vibeHasTracksOnPlaylist()">
                <h4>Tracks</h4>
                <div class="api-tracks">
                    <div v-for="track in vibes.show.api_tracks.on_playlist">
                        <vibe-track :track="track" :vibe="vibes.show"></vibe-track>
                    </div>
                </div>
            </div>
        </div>


        <div v-else-if="this.vibesHaveDeletedMessage()">
            <p v-text="this.vibes.deletedMessage"></p>
        </div>
    </div>
</template>

<script>
    import vibes from '../../core/vibes.js';
    import track from '../track/vibe-track.vue';
    import notifications from '../user/notifications.vue';
    import memberButtons from './partials/show/member-buttons';
    import members from './partials/show/members';
    import joinRequests from './partials/show/join-requests';
    import ownerButtons from './partials/show/owner-buttons';
    import showEdit from './partials/show/show-edit';

    export default {
        components: {
            'vibe-track': track,
            'user-notifications': notifications,
            'member-buttons': memberButtons,
            'members': members,
            'join-requests': joinRequests,
            'owner-buttons': ownerButtons,
            'show-edit': showEdit
        },

        data() {
            return {
                id: parseInt(this.$route.params.id),
                vibes: vibes
            }
        },

        created() {
            this.vibes.display(this.id);
        },

        methods: {
            vibeHasMessageToShow() {
                return this.vibes.message !== '';
            },

            vibesHaveDeletedMessage() {
                return this.vibes.deletedMessage !== '';
            },

            vibeHasTracksOnPlaylist() {
                return Object.keys(this.vibes.show.api_tracks.on_playlist).length > 0;
            }
        }
    }
</script>

<style scoped>
    .active {
        background: green;
    }
</style>