<template>
    <div>
        <div v-if="this.vibes.ownedByUser(vibeID)">
            <form method="POST" :action="vibes.routes.addTrack(vibeID, trackID)" @submit.prevent="onAddTrackSubmit">
                <input type="submit" name="track-vibe-add" :value="vibes.getVibeName(vibeID)">
            </form>
            <br>
        </div>
        <div v-else>
            <form method="POST" :action="vibes.routes.pendTrack(vibeID, trackID)" @submit.prevent="onPendTrackSubmit">
                <input type="submit" name="track-vibe-pend" :value="vibes.getVibeName(vibeID)">
            </form>
            <br>
        </div>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes.js';
    import Form from '../../../../classes/Form.js';

    export default {
        props: ['vibeID', 'trackID'],

        data() {
            return {
                vibes: vibes,
                addTrackForm: new Form({}),
                pendTrackForm: new Form({}),
            }
        },

        methods: {
            onAddTrackSubmit() {
                this.vibes.addTrack(this.addTrackForm, this.vibeID, this.trackID);
            },

            onPendTrackSubmit() {
                this.vibes.pendTrack(this.pendTrackForm, this.vibeID, this.trackID);
            }
        }
    }
</script>
