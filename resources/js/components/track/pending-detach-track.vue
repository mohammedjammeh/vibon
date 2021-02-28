<template>
    <div class="track-request">
        <div class="image">
            <img :src="this.track.album.images[0].url">
        </div>
        <div class="description-and-buttons">
            <p v-text="artistAndName(track)" style="white-space: nowrap; overflow: hidden;"></p>
            <p v-text="track.pending_to_detach_user" style="white-space: nowrap; overflow: hidden;"></p>

            <button>Accept</button>
            <button>Reject</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['track', 'category'],

        methods: {
            artistAndName(track) {
                return track.artists[0].name + ' - ' + track.name;
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

    img {
        max-width: 100%;
    }
</style>