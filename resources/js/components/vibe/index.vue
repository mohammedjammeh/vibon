<template>
    <div>
        <div v-if="this.vibes.loading">
            <p>loading..</p>
        </div>

        <div v-else-if="this.vibes.isEmpty()">
            <p>Rahh &#128562; No vibes around you, you gotta start one!</p>
        </div>

        <div v-else>
            <h4>My Vibes</h4>
            <div class="my-vibes">
                <div class="auto">
                    <h5>Auto</h5>
                    <index-list :vibes="ownerAutoVibes"></index-list>
                </div>
                <div class="manual">
                    <h5>Manual</h5>
                    <index-list :vibes="ownerManualVibes"></index-list>
                </div>
            </div>

            <br><br>

            <h4>Other Vibes</h4>
            <div class="other-vibes">
                <div class="auto">
                    <h5>Auto</h5>
                    <index-list :vibes="otherAutoVibes"></index-list>
                </div>
                <div class="manual">
                    <h5>Manual</h5>
                    <index-list :vibes="otherManualVibes"></index-list>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import vibes from '../../core/vibes';
    import user from '../../core/user';
    import indexList from '../partials/index-list';

    export default {
        components: {
            'index-list': indexList,
        },

        data() {
            return {
                vibes: vibes,
                user: user,
            }
        },

        created() {
            this.user.getVibesIDs();

            this.vibes.getAll().then(() => {
                this.vibes.sortVibesOrder();
            });
        },

        computed: {
            ownerAutoVibes: function() {
                return this.vibes.all.filter(vibe => vibe.auto_dj && vibe.destroyable)
            },

            ownerManualVibes: function () {
                return this.vibes.all.filter(vibe => !vibe.auto_dj && vibe.destroyable);
            },

            otherAutoVibes: function() {
                return this.vibes.all.filter(vibe => vibe.auto_dj  && !vibe.destroyable)
            },

            otherManualVibes: function () {
                return this.vibes.all.filter(vibe => !vibe.auto_dj  && !vibe.destroyable);
            },
        }
    }
</script>

<style scoped>
    .my-vibes,
    .other-vibes {
        overflow: auto;
    }

    .auto,
    .manual {
        width: 25%;
        float: left;
    }
</style>