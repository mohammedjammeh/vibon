<template>
    <div>
        <p v-if="this.loading">loading..</p>

        <p v-else-if="this.emptyVibes()">Rahh &#128562; No vibes around you, you gotta start one!</p>

        <div v-else>
            <div>
                <h5>Auto Vibes</h5>
                <ul>
                    <li v-for="vibe in this.vibes.all.filter(filterVibe => parseInt(filterVibe.auto_dj))">
                        <router-link :to="{ name: 'showVibe', params: { id: vibe.id }}" > {{ vibe.name }} </router-link> <br>
                    </li>
                </ul>
            </div>
            <br>

            <div>
                <h5>Manual Vibes</h5>
                <ul>
                    <li v-for="vibe in this.vibes.all.filter(filterVibe => !parseInt(filterVibe.auto_dj))">
                        <router-link :to="{ name: 'showVibe', params: { id: vibe.id }}" > {{ vibe.name }} </router-link> <br>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</template>

<script>
    import Vibes from '../../core/Vibes';
    import User from '../../core/User';

    export default {
        data() {
            return {
                vibes: Vibes,
                user: User,
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
            }
        }
    }
</script>