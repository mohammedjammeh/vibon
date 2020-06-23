<template>
    <div>
        <p v-if="this.loading">loading..</p>

        <p v-else-if="this.emptyVibes()">Rahh &#128562; No vibes around you, you gotta start one!</p>

        <div v-else>
            <div>
                <h5>Auto Vibes</h5>
                <ul>
                    <li v-for="vibe in filteredAutoVibes">
                        <router-link :to="{ name: 'showVibe', params: { id: vibe.id }}" :class="isShowing(vibe)">
                            <span v-if="isPlaying(vibe)">{{ vibe.name }} (playing)</span>
                            <span v-else>{{ vibe.name }}</span>
                        </router-link> <br>
                    </li>
                </ul>
            </div>
            <br>

            <div>
                <h5>Manual Vibes</h5>
                <ul>
                    <li v-for="vibe in filteredManualVibes">
                        <router-link :to="{ name: 'showVibe', params: { id: vibe.id }}" :class="isShowing(vibe)">
                            <span v-if="isPlaying(vibe)">{{ vibe.name }} (playing)</span>
                            <span v-else>{{ vibe.name }}</span>
                        </router-link> <br>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</template>

<script>
    import vibes from '../../core/vibes';
    import user from '../../core/user';

    export default {
        data() {
            return {
                vibes: vibes,
                user: user,
                loading: true
            }
        },

        created() {
            this.vibes.getAll()
                .then(() => this.loading = false);

            this.user.getVibesIDs();
        },

        methods: {
            emptyVibes() {
                return Object.keys(this.vibes.all).length === 0;
            },

            autoVibes() {
                return this.vibes.all.filter(vibe => vibe.auto_dj === 1);
            },

            manualVibes() {
                return this.vibes.all.filter(vibe => vibe.auto_dj === 0);
            },

            isShowing: function (vibe) {
                return vibe.id === this.vibes.showID ? 'showing' : '';
            },

            isPlaying: function (vibe) {
                return vibe.id === this.vibes.playingID;
            }
        },

        computed: {
            filteredAutoVibes: function() {
                return this.vibes.all.filter(filteredVibe => filteredVibe.auto_dj)
            },

            filteredManualVibes: function () {
                return this.vibes.all.filter(filteredVibe => !filteredVibe.auto_dj);
            }
        }
    }
</script>

<style scoped>
    .showing {
        color: brown;
    }
</style>