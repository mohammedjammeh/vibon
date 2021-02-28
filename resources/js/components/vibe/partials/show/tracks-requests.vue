<template>
    <div v-if="this.vibes.show.destroyable">
        <div>
            <div v-if="this.vibeHasPendingTracksToAttach()">
                <h4>Add tracks requests</h4>
                <div class="tracks-requests">
                    <div v-for="track in vibes.show.api_tracks.pending_to_attach">
                        <pending-attach-track :track="track"></pending-attach-track>
                    </div>
                </div>
                <br><br>
            </div>

            <div v-if="this.vibeHasPendingTracksToDetach()">
                <h4>Remove tracks requests</h4>
                <div class="tracks-requests">
                    <div v-for="track in vibes.show.api_tracks.pending_to_detach">
                        <pending-detach-track :track="track"></pending-detach-track>
                    </div>
                </div>
                <br><br>
            </div>

            <!--<form method="POST" :action="this.vibes.routes.autoRefresh(vibes.show.id)" @submit.prevent="onAutoRefreshSubmit" v-if="!this.vibes.show.auto_dj">-->
                <!--<input type="submit" name="vibe-tracks-update" value="Refresh">-->
                <!--<br><br>-->
            <!--</form>-->

            <!--<br><br>-->
        </div>

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
                // autoRefreshForm: new Form({}),
            }
        },

        methods: {
            vibeHasPendingTracksToAttach() {
                return Object.keys(this.vibes.show.api_tracks.pending_to_attach).length > 0;
            },
            vibeHasPendingTracksToDetach() {
                return Object.keys(this.vibes.show.api_tracks.pending_to_detach).length > 0;
            },
            // onAutoRefreshSubmit() {
            //     this.vibes.autoRefresh(this.autoRefreshForm, vibes.show.id);
            // },
        },

        computed: {
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
    }
</style>