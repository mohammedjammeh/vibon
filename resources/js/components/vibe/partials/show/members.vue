<template>
    <div>
        <h4>Members</h4>

        <div v-for="member in this.vibes.show.users">
            <p v-text="member.display_name"></p>

            <div v-if="!member.pivot.owner">
                <div v-if="vibes.show.destroyable">
                    <form method="POST" :action="vibes.routes.removeUser(this.id, member.id)" @submit.prevent="onRemoveUserSubmit(member.id)">
                        <input type="submit" name="vibe-member-remove" value="Remove">
                    </form>
                </div>
                <br>
            </div>
            <div v-else>
                <p>Owner</p>
                <br>
            </div>
        </div>

        <br><br>
    </div>
</template>

<script>
    import vibes from '../../../../core/vibes';
    import Form from '../../../../classes/Form.js';

    export default {
        data() {
            return {
                vibes: vibes,
                removeUserForm: new Form({}),
            }
        },

        methods: {
            onRemoveUserSubmit(memberID) {
                this.vibes.removeUser(this.removeUserForm, vibes.show.id, memberID);
            },
        },
    }
</script>

<style scoped>

</style>