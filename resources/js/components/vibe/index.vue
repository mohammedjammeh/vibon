<template>
    <div>
        <p v-if="this.loading">loading..</p>

        <p v-else-if="this.emptyVibes()">Rahh &#128562; No vibes around you, you gotta start one!</p>

        <ul v-else>
            <li v-for="vibe in this.vibes.all">
                <router-link :to="{ name: 'showVibe', params: { id: vibe.id }}" > {{ vibe.name }} </router-link> <br>
            </li>
        </ul>

    </div>
</template>

<script>
    import Vibes from '../../core/Vibes.js';

    export default {
        data() {
            return {
                vibes: Vibes,
                loading: true
            }
        },

        created() {
            this.vibes.getAll()
                .then(() => this.loading = false);
        },

        methods: {
            emptyVibes() {
                return Object.keys(this.vibes.all).length === 0;
            }
        }
    }
</script>