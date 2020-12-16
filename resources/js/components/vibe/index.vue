<template>
    <div>
        <div v-if="this.vibes.loading">
            <p>loading..</p>
        </div>

        <div v-else-if="this.vibes.isEmpty()">
            <p>Rahh &#128562; No vibes around you, you gotta start one!</p>
        </div>

        <div v-else>
            <h5>My vibes:</h5>
            <div class="my-vibes">
                <div class="auto">
                    <p>Auto</p>
                    <index-list :filtered-vibes="ownerAutoVibes"></index-list>
                </div>
                <div class="manual">
                    <p>Manual</p>
                    <index-list :filtered-vibes="ownerManualVibes"></index-list>
                </div>
            </div>

            <hr>

            <h5>Member of:</h5>
            <div class="my-vibes">
                <div class="auto">
                    <p>Auto</p>
                    <index-list :filtered-vibes="memberOfAutoVibes"></index-list>
                </div>
                <div class="manual">
                    <p>Manual</p>
                    <index-list :filtered-vibes="memberOfManualVibes"></index-list>
                </div>
            </div>

            <hr>

            <h5>Others to join:</h5>
            <div class="other-vibes">
                <div class="auto">
                    <p>Auto</p>
                    <index-list :filtered-vibes="otherAutoVibes"></index-list>
                </div>
                <div class="manual">
                    <p>Manual</p>
                    <index-list :filtered-vibes="otherManualVibes"></index-list>
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

            memberOfAutoVibes: function() {
                return this.vibes.all.filter(vibe => vibe.auto_dj && !vibe.destroyable && vibe.currentUserIsAMember)
            },

            memberOfManualVibes: function () {
                return this.vibes.all.filter(vibe => !vibe.auto_dj && !vibe.destroyable && vibe.currentUserIsAMember);
            },

            otherAutoVibes: function() {
                return this.vibes.all.filter(vibe => vibe.auto_dj  && !vibe.currentUserIsAMember)
            },

            otherManualVibes: function () {
                return this.vibes.all.filter(vibe => !vibe.auto_dj  && !vibe.currentUserIsAMember);
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
        width: 50%;
        float: left;
    }

    .auto p,
    .manual p {
        margin-bottom: 3px;
        font-weight: bold;
    }
</style>