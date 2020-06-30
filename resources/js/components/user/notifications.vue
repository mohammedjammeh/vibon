<template>
    <div v-if="this.loading">loading..</div>
    <div v-else>
        <li v-for="notification in user.notifications">
            <div v-if="isRequestToJoinVibe(notification)">
                <p>You have a join request from '{{ notification.data['requester_username'] }}'.</p>
            </div>
            <div v-else-if="isResponseToJoinVibe(notification)">
                <p v-if="notification.data['response']">
                    Your request to join '{{ vibes.getVibeName(notification.data['vibe_id'])}}' has been accepted.
                </p>
                <p v-else>
                    Your request to join '{{ vibes.getVibeName(notification.data['vibe_id'])}}' has been rejected.
                </p>
            </div>
            <div v-else-if="userLeftVibe(notification)">
                <p>'{{ notification.data.user_username }}' has left '{{ vibes.getVibeName(notification.data['vibe_id'])}}'.</p>
            </div>
            <div v-else-if="userJoinedVibe(notification)">
                <p>'{{ notification.data.user_username }}' has joined '{{ vibes.getVibeName(notification.data['vibe_id'])}}'.</p>
            </div>
            <div v-else-if="isRemovedFromVibe(notification)">
                <p>You have been removed from the '{{ vibes.getVibeName(notification.data['vibe_id']) }}' vibe.</p>
            </div>
        </li>
    </div>
</template>

<script>
    import user from '../../core/user.js';
    import vibes from '../../core/vibes.js';

    export default {
        data() {
            return {
                user: user,
                vibes: vibes,
                loading: true
            }
        },

        created() {
            user.getNotifications()
                .then(() => this.loading = false);
        },

        methods: {
            isRequestToJoinVibe(notification) {
                return notification.type === 'App\\Notifications\\RequestToJoinAVibe';
            },

            isResponseToJoinVibe(notification) {
                return notification.type === 'App\\Notifications\\ResponseToJoinAVibe';
            },

            isRemovedFromVibe(notification) {
                return notification.type === 'App\\Notifications\\RemovedFromAVibe';
            },

            userLeftVibe(notification) {
                return notification.type === 'App\\Notifications\\LeftVibe';
            },

            userJoinedVibe(notification) {
                return notification.type === 'App\\Notifications\\JoinedVibe';
            }
        }
    }
</script>