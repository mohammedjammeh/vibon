<template>
    <div class="track-request">
        <div class="image">
            <img :src="this.track.album.images[0].url">
        </div>
        <div class="description-and-buttons">
            <p v-text="artistAndTrackName"></p>
            <p v-text="byUser"></p>

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
        props: ['track', 'acceptedPendingTracksToDetach', 'rejectedPendingTracksToDetach'],

        data() {
            return {
                vibes: vibes,
            }
        },

        methods: {
            accept() {
                vibes.pendingTracksToAttachResponses[vibes.show.id].accepted.push(this.track.vibon_id);
                this.cancelReject();
            },
            reject() {
                vibes.pendingTracksToAttachResponses[vibes.show.id].rejected.push(this.track.vibon_id);
                this.cancelAccept();
            },
            cancelAccept() {
                let trackIdIndex =  vibes.pendingTracksToAttachResponses[vibes.show.id].accepted.indexOf(this.track.vibon_id);
                if (trackIdIndex !== -1) {
                    vibes.pendingTracksToAttachResponses[vibes.show.id].accepted.splice(trackIdIndex, 1);
                }
            },
            cancelReject() {
                let trackIdIndex =  vibes.pendingTracksToAttachResponses[vibes.show.id].rejected.indexOf(this.track.vibon_id);
                if (trackIdIndex !== -1) {
                    vibes.pendingTracksToAttachResponses[vibes.show.id].rejected.splice(trackIdIndex, 1);
                }
            },
        },

        computed: {
            isAccepted() {
                return this.acceptedPendingTracksToDetach.includes(this.track.vibon_id);
            },

            isRejected() {
                return this.rejectedPendingTracksToDetach.includes(this.track.vibon_id);
            },

            artistAndTrackName() {
                return this.track.artists[0].name + ' - ' + this.track.name;
            },

            byUser() {
                return 'Request by ' + this.track.pending_to_attach_user;
            }
        },
    }
</script>

<style scoped>
    .track-request {
        float: left;
        width: 50%;
        margin-bottom: 24px;
    }

    .track-request .image {
        float: left;
        width: 27%;
        margin-right: 5%;
    }
    .track-request .description-and-buttons {
        float: left;
        width: 68%;
    }

    .track-request .description-and-buttons p {
        margin: 0 0 8px 0;
        padding: 0;
        white-space: nowrap;
        overflow: hidden;
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
