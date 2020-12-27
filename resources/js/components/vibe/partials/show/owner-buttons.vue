<template>
    <div v-if="this.vibes.show.destroyable">
        <div>
            <form method="POST" :action="this.vibes.routes.delete(vibes.show.id)" @submit.prevent="onDeleteSubmit">
                <input type="submit" name="vibe-delete" value="Delete">
                <br><br>
            </form>

            <form method="POST" :action="this.vibes.routes.autoRefresh(vibes.show.id)" @submit.prevent="onAutoRefreshSubmit" v-if="this.vibes.show.auto_dj">
                <input type="submit" name="vibe-tracks-update" value="Refresh">
                <br><br>
            </form>

            <br><br>
        </div>


        <div class="synchronisation">
            <h4>Synced</h4>

            <div v-if="this.vibes.show.synced">
                <p>All Synced..</p>
            </div>

            <div v-else>
                <p>Sync using one of the two:</p>
                <form method="POST" :action="this.vibes.routes.syncVibe(vibes.show.id)" @submit.prevent="onSyncVibeSubmit">
                    <input type="submit" name="vibe" value="Vibe">
                </form>

                <form method="POST" :action="this.vibes.routes.syncPlaylist(vibes.show.id)" @submit.prevent="onSyncPlaylistSubmit">
                    <input type="submit" name="playlist" value="Playlist">
                </form>
                <br><br>
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
                deleteForm: new Form({}),
                autoRefreshForm: new Form({}),
                syncVibeForm: new Form({}),
                syncPlaylistForm: new Form({}),
            }
        },

        methods: {
            onDeleteSubmit() {
                this.vibes.delete(this.deleteForm, vibes.show.id);
            },

            onAutoRefreshSubmit() {
                this.vibes.autoRefresh(this.autoRefreshForm, vibes.show.id);
            },

            onSyncVibeSubmit() {
                this.vibes.syncVibe(this.syncVibeForm, vibes.show.id)
            },

            onSyncPlaylistSubmit() {
                this.vibes.syncPlaylist(this.syncPlaylistForm, vibes.show.id)
            }
        }
    }
</script>

<style scoped>
    .synchronisation form {
        display: inline-block;
    }
</style>