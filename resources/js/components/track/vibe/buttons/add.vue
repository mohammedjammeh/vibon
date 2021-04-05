<template>
    <div>
        <div v-if="this.vibes.ownedByUser(vibeID)">
            <form method="POST" :action="vibes.routes.addTrack(vibeID, trackApiId, actualCategory)" @submit.prevent="onAddTrackSubmit">
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
    import vibes from '../../../../core/vibes.js';
    import tracks from '../../../../core/tracks.js';
    import Form from '../../../../classes/Form.js';

    export default {
         props: ['vibeID', 'trackApiId', 'category'],

        data() {
            return {
                vibes: vibes,
                tracks: tracks,
                addTrackForm: new Form({}),
                pendAttachTrackForm: new Form({}),
            }
        },

        methods: {
            onAddTrackSubmit() {
                this.vibes.addTrack(this.addTrackForm, this.vibeID, this.trackApiId, this.actualCategory);
            },

            onPendAttachTrackSubmit() {
                this.vibes.pendAttachTrack(this.pendAttachTrackForm, this.vibeID, this.trackApiId);
            }
        },

        computed: {
            actualCategory() {
                 let vibe = this.vibes.all.find((vibe) => vibe.id === this.vibeID);
                 let tracksNotOnVibon = vibe.api_tracks.not_on_vibon;
                 let tracksNotOnVibonIDs = tracksNotOnVibon.map((track) => track.id);

                 if(tracksNotOnVibonIDs.includes(this.trackApiId)) {
                     return this.tracks.categories.not_on_vibon;
                 }

                 return this.category;
             }
        }
    }
</script>
