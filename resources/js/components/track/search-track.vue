<template>
    <div :class="isPlaying">
        <play :track="track" :searchTracks="searchTracks"></play>
        <div class="track">
            <div v-for="userVibeID in user.memberOfVibesIDs">
                <div class="track-name">
                    <p v-text="vibes.getVibeName(userVibeID)"></p>
                </div>

                <div class="track-buttons">
                    <cancel-pending-detach-track-button
                        v-if="track.pending_vibes_to_detach.includes(userVibeID)"
                        :vibeID="userVibeID"
                        :trackID="track.vibon_id"
                        :searchTracks="searchTracks"
                    >
                    </cancel-pending-detach-track-button>

                    <remove-button
                        v-else-if="track.vibes.includes(userVibeID)"
                        :vibeID="userVibeID"
                        :trackID="track.vibon_id"
                        :searchTracks="searchTracks"
                    >
                    </remove-button>

                    <cancel-pending-attach-track-button
                        v-else-if="track.pending_vibes_to_attach.includes(userVibeID)"
                        :vibeID="userVibeID"
                        :trackID="track.vibon_id"
                        :searchTracks="searchTracks"
                    >
                    </cancel-pending-attach-track-button>

                    <add-button
                        v-else
                        :vibeID="userVibeID"
                        :trackApiId="track.id"
                        :searchTracks="searchTracks"
                    >
                    </add-button>
                </div>
            </div>

        <!--<div class="track">-->
            <!--&lt;!&ndash;<p>Manual Vibes</p>&ndash;&gt;-->
            <!--<div v-for="userVibeID in user.manualVibesIDs">-->
                <!--<remove-button :userVibeID="userVibeID" :trackID="track.vibon_id" :searchTracks="searchTracks" v-if="track.vibes.includes(userVibeID)"></remove-button>-->
                <!--<add-button :userVibeID="userVibeID" :trackID="track.id" :searchTracks="searchTracks" v-else></add-button>-->
            <!--</div>-->

            <!--&lt;!&ndash;<p>Auto Vibes</p>&ndash;&gt;-->
            <!--<div v-for="userVibeID in user.autoVibesIDs">-->
                <!--<remove-button :userVibeID="userVibeID" :trackID="track.vibon_id" :searchTracks="searchTracks" v-if="track.vibes.includes(userVibeID)"></remove-button>-->
                <!--<add-button :userVibeID="userVibeID" :trackID="track.id" :searchTracks="searchTracks" v-else></add-button>-->
            <!--</div>-->
        </div>
    </div>
</template>

<script>
    import user from '../../core/user.js';
    import vibes from '../../core/vibes.js';
    import search from '../../core/search.js';
    import play from './search/partials/play';
    import addButton from './search/buttons/add';
    import removeButton from './search/buttons/remove';
    import cancelPendingAttachTrackButton from './search/buttons/cancel-pending-attach-track';
    import cancelPendingDetachTrackButton from './search/buttons/cancel-pending-detach-track';

    export default {
        props: ['track', 'searchTracks'],

        components: {
            'play' : play,
            'add-button': addButton,
            'remove-button': removeButton,
            'cancel-pending-attach-track-button': cancelPendingAttachTrackButton,
            'cancel-pending-detach-track-button': cancelPendingDetachTrackButton,
        },

        data() {
            return {
                search: search,
                user: user,
                vibes: vibes
            }
        },

        computed : {
            isPlaying() {
                if(this.search.playingTrack === this.track.id) {
                    return 'playback-play-track-search playing';
                }
                return 'playback-play-track-search';
            }
        }
    }
</script>

<style scoped>
    .playing {
        background: green;
    }

    .track {
        overflow: auto;
    }

    .track-name {
        float: left;
        width: 30%;
        margin-top: 3px;
    }

    .track-buttons {
        float: left;
        width: 70%;
    }

    .track-buttons div {
        float: right;
    }
</style>
