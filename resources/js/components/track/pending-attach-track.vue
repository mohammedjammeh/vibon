<template>
    <div class="track-request">
        <div class="image">
            <img :src="this.track.album.images[0].url">
        </div>
        <div class="description-and-buttons">
            <p v-text="artistAndTrackName" style="white-space: nowrap; overflow: hidden;"></p>
            <p v-text="track.pending_to_attach_user" style="white-space: nowrap; overflow: hidden;"></p>

            <button v-if="isAccepted" @click="cancelAccept" class='accepted'>Accept</button>
            <button v-else @click="accept">Accept</button>

            <button v-if="isRejected" @click="cancelReject" class='rejected'>Reject</button>
            <button v-else @click="reject">Reject</button>
        </div>
    </div>
</template>

<script>
    import vibes from '../../core/vibes.js';

    export default {
        props: ['track', 'category'],

        data() {
            return {
                vibes: vibes,
                pendingTracksToAttach: vibes.pendingTracksResponses[vibes.show.id].to_attach,
                pendingTracksToDetach: vibes.pendingTracksResponses[vibes.show.id].to_detach,
            }
        },

        methods: {
            accept() {
                vibes.pendingTracksResponses[vibes.show.id].to_attach.push(this.track.id);
                this.cancelReject();
            },
            reject() {
                vibes.pendingTracksResponses[vibes.show.id].to_detach.push(this.track.id);
                this.cancelAccept();
            },
            cancelAccept() {
                let trackIdIndex =  vibes.pendingTracksResponses[vibes.show.id].to_attach.indexOf(this.track.id);
                if (trackIdIndex !== -1) {
                    vibes.pendingTracksResponses[vibes.show.id].to_attach.splice(trackIdIndex, 1);
                }
            },
            cancelReject() {
                let trackIdIndex =  vibes.pendingTracksResponses[vibes.show.id].to_detach.indexOf(this.track.id);
                if (trackIdIndex !== -1) {
                    vibes.pendingTracksResponses[vibes.show.id].to_detach.splice(trackIdIndex, 1);
                }
            },
        },

        computed: {
            isAccepted() {
                return this.pendingTracksToAttach.includes(this.track.id);
            },

            isRejected() {
                return this.pendingTracksToDetach.includes(this.track.id);
            },

            artistAndTrackName() {
                return this.track.artists[0].name + ' - ' + this.track.name;
            },
        },
    }
</script>

<style scoped>

    .track-request {
        float: left;
        width: 50%;
    }

    .track-request .image {
        float: left;
        width: 22%;
        margin-right: 3%;
    }
    .track-request .description-and-buttons {
        float: left;
        width: 75%;
    }

    .track-request .description-and-buttons p {
        margin: 0;
        padding: 0;
    }

    .track-request .description-and-buttons button {
        margin-top: 4px;
        border: 1px solid lightgrey;
    }

    .track-request .description-and-buttons button:focus {
        outline: 0;
    }

    .track-request .description-and-buttons button.accepted,
    .track-request .description-and-buttons button.rejected {
        background: green;
    }

    img {
        max-width: 100%;
    }
</style>