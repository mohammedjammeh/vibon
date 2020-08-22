<template>
    <div>
        <!--<form method="POST" :action="vibes.routes.addTrack(userVibeID, trackID)" @submit.prevent="onAddTrackSubmit(userVibeID, trackID)">-->
            <!--<input type="submit" name="track-vibe-destroy" :value="vibes.getVibeName(userVibeID)">-->
        <!--</form>-->
        <!--<br>-->
        <form method="POST" :action="vibes.routes.addTrack(userVibeID, trackID)" @submit.prevent="onAddTrackSubmit(userVibeID, trackID)">
            <input type="submit" name="track-vibe-destroy" :value="vibes.getVibeName(userVibeID)">
        </form>
        <br>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes.js';
    import Form from '../../../../classes/Form.js';

    export default {
        props: ['userVibeID', 'trackID', 'searchTracks'],

        data() {
            return {
                vibes: vibes,
                addTrackForm: new Form({})
            }
        },

        methods: {
            onAddTrackSubmit(vibeID, trackID) {
                this.vibes.addTrack(this.addTrackForm, vibeID, trackID);
                let track =  this.searchTracks.find(track => track.id === trackID);
                track.vibes.push(vibeID);
            }
        }
    }
</script>
