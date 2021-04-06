<template>
    <div>
        <div v-if="this.vibes.ownedByUser(vibeID)">
            <form method="POST" :action="vibes.routes.removeTrack(vibeID, trackID)" @submit.prevent="onRemoveTrackSubmit">
                <input type="submit" name="track-vibe-destroy" value="Remove">
            </form>
            <br>
        </div>
        <div v-else>
            <form method="POST" :action="vibes.routes.pendDetachTrack(vibeID, trackID)" @submit.prevent="onPendDetachTrackSubmit">
                <input type="submit" name="track-vibe-pend" value="Remove Request">
            </form>
            <br>
        </div>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes.js';
    import Form from '../../../../classes/Form.js';

    export default {
        props: ['vibeID', 'trackID', 'searchTracks'],

        data() {
            return {
                vibes: vibes,
                removeTrackForm: new Form({}),
                pendDetachTrackForm: new Form({}),
            }
        },

        methods: {
            onRemoveTrackSubmit() {
                this.vibes.removeTrack(this.removeTrackForm, this.vibeID, this.trackID);
                let track =  this.searchTracks.find(track => track.vibon_id === this.trackID);
                this.vibes.removeVibeFromTrackVibes(this.vibeID, track);
            },

            onPendDetachTrackSubmit() {
                this.vibes.pendDetachTrack(this.pendDetachTrackForm, this.vibeID, this.trackID);
                let track =  this.searchTracks.find(track => track.vibon_id === this.trackID);
                this.vibes.addVibeToTrackPendingVibesToDetach(this.vibeID, track);
            }
        }
    }
</script>

<style scoped>
    form input {
        background: #b53737;
        color: white;
    }
</style>
