<template>
    <div v-if="this.vibes.show.destroyable">

        <h4>Join requests</h4>

        <div v-if="this.vibeHasJoinRequests()">
            <div v-for="joinRequest in this.vibes.show.requests">
                <p v-text="joinRequest.user.display_name"></p>

                <form method="POST" :action="vibes.routes.acceptJoinRequest(joinRequest.id)" @submit.prevent="onAcceptJoinRequestSubmit(joinRequest.id)">
                    <input type="submit" name="accept" value="Accept">
                </form>
                <br>

                <form method="POST" :action="vibes.routes.rejectJoinRequest(joinRequest.id)" @submit.prevent="onRejectJoinRequestSubmit(joinRequest.id)">
                    <input type="submit" name="reject" value="Reject">
                </form>
                <br>
            </div>
        </div>

        <div v-else>
            <p>No join requests..</p>
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
                acceptJoinRequestForm: new Form({}),
                rejectJoinRequestForm: new Form({}),
            }
        },

        methods: {
            vibeHasJoinRequests() {
                return Object.keys(this.vibes.show.requests).length > 0;
            },

            onAcceptJoinRequestSubmit(requestID) {
                this.vibes.acceptJoinRequest(this.acceptJoinRequestForm, requestID);
            },

            onRejectJoinRequestSubmit(requestID) {
                this.vibes.rejectJoinRequest(this.rejectJoinRequestForm, requestID);
            },
        }
    }
</script>

<style scoped>
</style>