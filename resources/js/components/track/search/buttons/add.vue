<template>
    <div>
        <div v-if="this.vibes.ownedByUser(vibeID)">
            <form method="POST" :action="vibes.routes.addTrack(vibeID, trackApiId, trackCategory)" @submit.prevent="onAddTrackSubmit">
                <input type="submit" name="track-vibe-add" value="Add">
            </form>
            <br>
        </div>
        <div v-else>
            <form method="POST" :action="vibes.routes.pendAttachTrack(vibeID, trackApiId)" @submit.prevent="onPendAttachTrackSubmit">
                <input type="submit" name="track-vibe-pend" value="Add Request">
            </form>
            <br>
        </div>
    </div>
</template>

<script>
    import Form from '../../../../classes/Form.js';
    import vibes from '../../../../core/vibes.js';
    import trackCore from '../../../../core/track.js';

    export default {
        props: ['vibeID', 'trackApiId', 'searchTracks'],

        data() {
            return {
                vibes: vibes,
                trackCore: trackCore,
                addTrackForm: new Form({}),
                pendAttachTrackForm: new Form({}),
            }
        },

        methods: {
            onAddTrackSubmit() {
                this.vibes.addTrack(this.addTrackForm, this.vibeID, this.trackApiId, this.trackCategory);
                let track =  this.searchTracks.find(track => track.id === this.trackApiId);
                this.vibes.addVibeToTrackVibes(this.vibeID, track);
            },

            onPendAttachTrackSubmit() {
                this.vibes.pendAttachTrack(this.pendAttachTrackForm, this.vibeID, this.trackApiId);
                let track =  this.searchTracks.find(track => track.id === this.trackApiId);
                this.vibes.addVibeToTrackPendingVibesToAttach(this.vibeID, track);
            }
        },

        computed: {
            trackCategory() {
                return trackCore.category(this.vibeID, this.trackApiId, this.category);
            }
        }
    }
</script>
