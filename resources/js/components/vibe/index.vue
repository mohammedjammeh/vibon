<template>
    <div>
        <p v-if="this.loading">loading..</p>

        <p v-else-if="this.emptyVibes()">Rahh &#128562; No vibes around you, you gotta start one!</p>

        <ul v-else>
            <li v-for="vibe in this.vibes" >
                <router-link :to="{ name: 'showVibe', params: { id: vibe.id }}"> {{ vibe.name }} </router-link> <br>
            </li>
        </ul>

    </div>
</template>

<script>
    export default {
        props: ['route'],
        data() {
            return {
                vibes: {},
                loading: true
            }
        },
        created() {
            axios.get(this.route)
                .then(response => this.vibes = response.data.vibes)
                .catch(error => console.log(error))
                .finally(() => this.loading = false);

        },
        methods: {
            emptyVibes() {
                return Object.keys(this.vibes).length === 0;
            }
        }
    }
</script>