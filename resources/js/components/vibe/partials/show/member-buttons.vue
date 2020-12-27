<template>
    <div v-if="!this.vibes.show.destroyable">
        <div v-if="this.vibes.show.currentUserIsAMember">
            <form method="POST" :action="vibes.routes.leaveVibe(vibes.show.id)" @submit.prevent="onLeaveVibeSubmit">
                <input type="submit" name="vibe-leave" value="Leave Vibe">
            </form>
        </div>

        <div v-else-if="this.vibes.show.hasJoinRequestFromUser">
            <form method="POST" :action="vibes.routes.cancelJoinRequest(vibes.show.joinRequestFromUser.id)" @submit.prevent="onCancelJoinRequestSubmit(vibes.show.joinRequestFromUser.id)">
                <input type="submit" name="vibe-join-destroy" value="Cancel Join Request">
            </form>
        </div>

        <div v-else>
            <div v-if="this.vibes.show.open">
                <form method="POST" :action="vibes.routes.joinVibe(vibes.show.id)" @submit.prevent="onJoinVibeSubmit">
                    <input type="submit" name="vibe-store" value="Join Vibe">
                </form>
            </div>

            <div v-else>
                <form method="POST" :action="vibes.routes.sendJoinRequest(vibes.show.id)" @submit.prevent="onSendJoinRequestSubmit">
                    <input type="submit" name="vibe-store" value="Send Join Request">
                </form>
            </div>
        </div>

        <br><br>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes';
    import Form from '../../../../classes/Form.js';

    export default {
        data() {
            return {
                vibes: vibes,
                sendJoinRequestForm: new Form({}),
                cancelJoinRequestForm: new Form({}),
                leaveVibeForm: new Form({}),
                joinVibeForm: new Form({}),
            }
        },

        methods: {
            onSendJoinRequestSubmit() {
                this.vibes.sendJoinRequest(this.sendJoinRequestForm, vibes.show.id);
            },

            onCancelJoinRequestSubmit(requestID) {
                this.vibes.cancelJoinRequest(this.cancelJoinRequestForm, requestID);
            },

            onLeaveVibeSubmit() {
                this.vibes.leaveVibe(this.leaveVibeForm, vibes.show.id);
            },

            onJoinVibeSubmit() {
                this.vibes.joinVibe(this.joinVibeForm, vibes.show.id);
            },
        },
    }
</script>

<style scoped>

</style>