<template>
    <div v-if="this.vibes.show.destroyable">
        <h4>Add tracks requests</h4>
        <div v-if="this.vibeHasPendingTracksToAttach()">
            <div class="tracks-requests">
                <div v-for="track in vibes.show.api_tracks.pending_to_attach">
                    <pending-attach-track
                        :track="track"
                        :acceptedPendingTracksToAttach="acceptedPendingTracksToAttach"
                        :rejectedPendingTracksToAttach="rejectedPendingTracksToAttach"
                    >
                    </pending-attach-track>
                </div>
            </div>

            <form method="POST" :action="this.vibes.routes.sendPendingTracksToAttachResponses(vibes.show.id)" @submit.prevent="onSendPendingTracksToAttachResponses">
                <input type="submit" name="send-pending-tracks-to-attach-responses" value="Send Pending Tracks To Add Responses" :disabled="noPendingTracksToAttachResponses">
                <br><br>
            </form>
        </div>
        <div v-else>
            <p>No requests to add tracks..</p>
        </div>
        <br><br>

        <h4>Remove tracks requests</h4>
        <div v-if="this.vibeHasPendingTracksToDetach()">
            <div class="tracks-requests">
                <div v-for="track in vibes.show.api_tracks.pending_to_detach">
                    <pending-detach-track
                        :track="track"
                        :acceptedPendingTracksToDetach="acceptedPendingTracksToDetach"
                        :rejectedPendingTracksToDetach="rejectedPendingTracksToDetach"
                    >
                    </pending-detach-track>
                </div>
            </div>
            <form method="POST" :action="this.vibes.routes.sendPendingTracksToDetachResponses(vibes.show.id)" @submit.prevent="onSendPendingTracksToDetachResponses">
                <input type="submit" name="send-pending-tracks-to-detach-responses" value="Send Pending Tracks To Remove Responses" :disabled="noPendingTracksToDetachResponses">
                <br><br>
            </form>
        </div>
        <div v-else>
            <p>No requests to remove tracks..</p>
        </div>
        <br><br>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes';
    import Form from '../../../../classes/Form.js';
    import pendingAttachTrack from '../../../track/pending-attach-track.vue';
    import pendingDetachTrack from '../../../track/pending-detach-track.vue';

    export default {
        components: {
            'pending-attach-track': pendingAttachTrack,
            'pending-detach-track': pendingDetachTrack,
        },

        data() {
            return {
                vibes: vibes,
                sendPendingTracksToAttachResponsesForm: new Form({}),
                sendPendingTracksToDetachResponsesForm: new Form({}),

                acceptedPendingTracksToAttach: vibes.pendingTracksToAttachResponses[vibes.show.id].accepted,
                rejectedPendingTracksToAttach: vibes.pendingTracksToAttachResponses[vibes.show.id].rejected,

                acceptedPendingTracksToDetach: vibes.pendingTracksToDetachResponses[vibes.show.id].accepted,
                rejectedPendingTracksToDetach: vibes.pendingTracksToDetachResponses[vibes.show.id].rejected,
            }
        },

        methods: {
            vibeHasPendingTracksToAttach() {
                return Object.keys(this.vibes.show.api_tracks.pending_to_attach).length > 0;
            },
            vibeHasPendingTracksToDetach() {
                return Object.keys(this.vibes.show.api_tracks.pending_to_detach).length > 0;
            },
            onSendPendingTracksToAttachResponses() {
                this.vibes.sendPendingTracksToAttachResponses(this.sendPendingTracksToAttachResponsesForm, this.vibes.show.id);
            },
            onSendPendingTracksToDetachResponses() {
                this.vibes.sendPendingTracksToDetachResponses(this.sendPendingTracksToDetachResponsesForm, this.vibes.show.id);
            },
        },

        computed: {
            noPendingTracksToAttachResponses() {
                return this.acceptedPendingTracksToAttach.length === 0 && this.rejectedPendingTracksToAttach.length === 0;
            },

            noPendingTracksToDetachResponses() {
                return this.acceptedPendingTracksToDetach.length === 0 && this.rejectedPendingTracksToDetach.length === 0;
            },
        }
    }
</script>

<style scoped>
    .tracks-requests {
        width: 100%;
        height: 100%;
        padding-top: 10px;
        overflow-x: auto;
        white-space: nowrap;
        margin-bottom: 10px;
    }
</style>