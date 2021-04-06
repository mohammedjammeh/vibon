<template>
    <div>
        <div v-if="this.vibes.ownedByUser(vibeID)">
            <form method="POST" :action="vibes.routes.addTrack(vibeID, trackApiId, trackCategory)" @submit.prevent="onAddTrackSubmit">
                <input type="submit" name="track-vibe-add" value="Add track">
            </form>
            <br>
        </div>
        <div v-else>
            <form method="POST" :action="vibes.routes.pendAttachTrack(vibeID, trackApiId)" @submit.prevent="onPendAttachTrackSubmit">
                <input type="submit" name="track-vibe-pend" value="Add track request">
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
         props: ['vibeID', 'trackApiId', 'category'],

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
            },

            onPendAttachTrackSubmit() {
                this.vibes.pendAttachTrack(this.pendAttachTrackForm, this.vibeID, this.trackApiId);
            }
        },

        computed: {
            trackCategory() {
                return trackCore.category(this.vibeID, this.trackApiId, this.category);
             }
        }
    }
</script>
