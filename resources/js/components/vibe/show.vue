<template>
    <div>
        <div v-if="this.vibes.readyToShow()">
            <div v-if="this.vibes.message !== '' ">
                <p v-text="this.vibes.message"></p>
                <br>
            </div>

            <div v-if="!this.editMode">
                <p v-text="this.vibes.show.name"></p>
                <p v-text="this.vibes.show.description"></p>

                <p v-if="this.vibes.show.open">Opened</p>
                <p v-else>Not Opened</p>

                <p v-if="this.vibes.show.auto_dj">Auto DJ</p>
                <p v-else>Manual DJ</p>
            </div>
            <br>

            <user-notifications :notifications="this.vibes.show.notifications"></user-notifications>
            <br><br>

            <div v-if="this.vibes.show.destroyable">
                <div v-if="this.editMode">
                    <form method="POST" :action="this.vibes.routes.update(this.id)" @submit.prevent="onUpdateSubmit" autocomplete="off">
                        <div>
                            <input type="text" name="name" placeholder="Name" v-model="editForm.name" @keydown="editForm.errors.clear('name')">
                            <span v-text="editForm.errors.get('name')" v-if="editForm.errors.has('name')"></span>
                        </div>
                        <br>

                        <div>
                            <p>Open</p>

                            <input type="radio" name="open" id="open-yes" v-model="editForm.open" value="1" @click="editForm.errors.clear('open')">
                            <label for="open-yes">Yes</label>

                            <input type="radio" name="open" id="open-no" v-model="editForm.open" value="0" @click="editForm.errors.clear('open')">
                            <label for="open-no">No</label>

                            <span v-text="editForm.errors.get('open')" v-if="editForm.errors.has('open')"></span>
                        </div>
                        <br>

                        <div>
                            <p>Auto DJ:</p>

                            <input type="radio" name="auto_dj" id="auto-dj-yes" v-model="editForm.auto_dj" value="1" @click="editForm.errors.clear('auto_dj')">
                            <label for="auto-dj-yes">Yes</label>

                            <input type="radio" name="auto_dj" id="auto-dj-no" v-model="editForm.auto_dj" value="0" @click="editForm.errors.clear('auto_dj')">
                            <label for="auto-dj-no">No</label>

                            <span v-text="editForm.errors.get('auto_dj')" v-if="editForm.errors.has('auto_dj')"></span>
                        </div>
                        <br>

                        <div>
                            <textarea name="description" cols="19" rows="5" placeholder="Description" v-model="editForm.description" @keydown="editForm.errors.clear('description')"></textarea>
                            <span v-text="editForm.errors.get('description')" v-if="editForm.errors.has('description')"></span>
                        </div>
                        <br>

                        <div>
                            <input type="submit" name="vibe-edit" value="Update" :disabled="editForm.errors.any()">
                        </div>
                    </form>
                </div>
                <div v-else>
                    <button @click="this.turnOnEditMode">Edit</button>
                </div>
                <br>

                <form method="POST" :action="this.vibes.routes.delete(this.id)" @submit.prevent="onDeleteSubmit">
                    <div>
                        <input type="submit" name="vibe-delete" value="Delete">
                    </div>
                </form>
                <br>

                <form method="POST" :action="this.vibes.routes.autoRefresh(this.id)" @submit.prevent="onAutoRefreshSubmit" v-if="this.vibes.show.auto_dj">
                    <input type="submit" name="vibe-tracks-update" value="Refresh">
                </form>
                <br>

                <div v-if="this.vibes.show.synced">
                    <p>All Synced</p>
                </div>
                <div v-else>
                    <p>Sync using one of the two:</p>
                    <form method="POST" :action="this.vibes.routes.syncVibe(this.id)" @submit.prevent="onSyncVibeSubmit">
                        <input type="submit" name="vibe" value="Vibe">
                    </form>
                    <br>
                    <form method="POST" :action="this.vibes.routes.syncPlaylist(this.id)" @submit.prevent="onSyncPlaylistSubmit">
                        <input type="submit" name="playlist" value="Playlist">
                    </form>
                </div>
                <br><br>

                <div v-if="this.vibeHasJoinRequests()">
                    <h4>Requests</h4>
                    <div v-for="joinRequest in this.vibes.show.requests">
                        <p v-text="joinRequest.user.display_name"></p>
                        <form method="POST" :action="vibes.routes.acceptJoinRequest(joinRequest.id)" @submit.prevent="onAcceptJoinRequestSubmit(joinRequest.id)">
                            <input type="submit" name="accept" value="Accept">
                        </form>
                        <br>

                        <form method="POST" :action="vibes.routes.rejectJoinRequest(joinRequest.id)" @submit.prevent="onRejectJoinRequestSubmit(joinRequest.id)">
                            <input type="submit" name="reject" value="Reject">
                        </form>
                        <br><br>
                    </div>
                </div>
            </div>

            <div v-else>
                <div v-if="this.vibes.show.currentUserIsAMember">
                    <form method="POST" :action="vibes.routes.leaveVibe(this.id)" @submit.prevent="onLeaveVibeSubmit">
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
                        <form method="POST" :action="vibes.routes.joinVibe(this.id)" @submit.prevent="onJoinVibeSubmit">
                            <input type="submit" name="vibe-store" value="Join Vibe">
                        </form>
                    </div>
                    <div v-else>
                        <form method="POST" :action="vibes.routes.sendJoinRequest(this.id)" @submit.prevent="onSendJoinRequestSubmit">
                            <input type="submit" name="vibe-store" value="Send Join Request">
                        </form>
                    </div>
                </div>
            </div>
            <br><br>

            <div>
                <h4>Members</h4>
                <div v-for="member in this.vibes.show.users">
                    <p v-text="member.display_name"></p>

                    <div v-if="!member.pivot.owner">
                        <div v-if="vibes.show.destroyable">
                            <form method="POST" :action="vibes.routes.removeUser(this.id, member.id)" @submit.prevent="onRemoveUserSubmit(member.id)">
                                <input type="submit" name="vibe-member-remove" value="Remove">
                            </form>
                        </div>
                        <br>
                    </div>
                    <div v-else>
                        <p>Owner</p>
                        <br>
                    </div>
                </div>
            </div>
            <br><br>

            <div v-if="this.vibeHasTracks()">
                <h4>Tracks</h4>
                <div class="api-tracks">
                    <div v-for="track in vibes.show.api_tracks">
                        <vibe-track :track="track" :vibe="vibes.show"></vibe-track>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="this.vibes.deletedMessage !== '' ">
            <p v-text="this.vibes.deletedMessage"></p>
        </div>
    </div>
</template>

<script>
    import vibes from '../../core/vibes.js';
    import user from '../../core/user.js';
    import playback from '../../core/playback.js';
    import Form from '../../classes/Form.js';
    import track from '../track/vibe-track.vue';
    import notifications from '../user/notifications.vue';

    export default {
        components: {
            'vibe-track': track,
            'user-notifications': notifications,
        },

        data() {
            return {
                id: parseInt(this.$route.params.id),
                vibes: vibes,
                user: user,
                playback: playback,
                editMode: false,
                editForm: new Form({
                    name: '',
                    description: '',
                    open: '',
                    auto_dj: '',
                }),
                deleteForm: new Form({}),
                autoRefreshForm: new Form({}),
                syncVibeForm: new Form({}),
                syncPlaylistForm: new Form({}),
                acceptJoinRequestForm: new Form({}),
                rejectJoinRequestForm: new Form({}),
                sendJoinRequestForm: new Form({}),
                cancelJoinRequestForm: new Form({}),
                leaveVibeForm: new Form({}),
                joinVibeForm: new Form({}),
                removeUserForm: new Form({})
            }
        },

        created() {
            this.vibes.display(this.id);
        },

        methods: {
            vibeHasTracks() {
                return Object.keys(this.vibes.show.api_tracks).length > 0;
            },

            vibeHasJoinRequests() {
                return Object.keys(this.vibes.show.requests).length > 0;
            },

            onDeleteSubmit() {
                this.vibes.delete(this.deleteForm, this.id);
            },

            onUpdateSubmit() {
                this.vibes.update(this.editForm, this.id)
                    .then(() => this.editMode = false);
            },

            onAutoRefreshSubmit() {
                this.vibes.autoRefresh(this.autoRefreshForm, this.id);
            },

            onAcceptJoinRequestSubmit(requestID) {
                this.vibes.acceptJoinRequest(this.acceptJoinRequestForm, requestID);
            },

            onRejectJoinRequestSubmit(requestID) {
                this.vibes.rejectJoinRequest(this.rejectJoinRequestForm, requestID);
            },

            onSendJoinRequestSubmit() {
                this.vibes.sendJoinRequest(this.sendJoinRequestForm, this.id);
            },

            onCancelJoinRequestSubmit(requestID) {
                this.vibes.cancelJoinRequest(this.cancelJoinRequestForm, requestID);
            },

            onLeaveVibeSubmit() {
                this.vibes.leaveVibe(this.leaveVibeForm, this.id);
            },

            onJoinVibeSubmit() {
                this.vibes.joinVibe(this.joinVibeForm, this.id);
            },

            onRemoveUserSubmit(memberID) {
                this.vibes.removeUser(this.joinVibeForm, this.id, memberID);
            },

            turnOnEditMode() {
                this.editForm.name = this.vibes.show.name;
                this.editForm.open = + this.vibes.show.open;
                this.editForm.auto_dj = + this.vibes.show.auto_dj;
                this.editForm.description = this.vibes.show.description;
                this.editMode = true;
            },

            onSyncVibeSubmit() {
                this.vibes.syncVibe(this.syncVibeForm, this.id)
            },

            onSyncPlaylistSubmit() {
                this.vibes.syncPlaylist(this.syncPlaylistForm, this.id)
            }
        }
    }
</script>

<style scoped>
    .active {
        background: green;
    }
</style>