<template>
    <div>
        <div v-if="this.vibes.readyToShow()">
            <tracks-requests :key="this.vibes.show.id"></tracks-requests>

            <div v-if="this.vibeHasMessageToShow()">
                <p v-text="this.vibes.message"></p>
                <br>
            </div>

            <attributes></attributes>

            <owner-buttons></owner-buttons>

            <join-requests></join-requests>

            <member-buttons></member-buttons>

            <user-notifications></user-notifications>

            <members></members>

            <tracks></tracks>
        </div>


        <div v-else-if="this.vibesHaveDeletedMessage()">
            <p v-text="this.vibes.deletedMessage"></p>
        </div>
    </div>
</template>

<script>
    import vibes from '../../core/vibes.js';
    import user from '../../core/user.js';
    import notifications from '../user/notifications.vue';
    import memberButtons from './partials/show/member-buttons';
    import members from './partials/show/members';
    import joinRequests from './partials/show/join-requests';
    import tracksRequests from './partials/show/tracks-requests';
    import ownerButtons from './partials/show/owner-buttons';
    import attributes from './partials/show/attributes';
    import tracks from './partials/show/tracks';

    export default {
        components: {
            'user-notifications': notifications,
            'member-buttons': memberButtons,
            'members': members,
            'join-requests': joinRequests,
            'tracks-requests': tracksRequests,
            'owner-buttons': ownerButtons,
            'attributes': attributes,
            'tracks': tracks
        },

        data() {
            return {
                id: parseInt(this.$route.params.id),
                vibes: vibes,
                user: user,
            }
        },

        created() {
            if (user.id !== null) {
                this.vibes.display(this.id);
                return;
            }
            
            user.getID()
                .then(() => {
                    this.vibes.display(this.id);
                });
        },

        methods: {
            vibeHasMessageToShow() {
                return this.vibes.message !== '';
            },

            vibesHaveDeletedMessage() {
                return this.vibes.deletedMessage !== '';
            }
        }
    }
</script>

<style scoped>
    .active {
        background: green;
    }
</style>