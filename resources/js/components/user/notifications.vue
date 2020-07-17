<template>
    <div v-if="this.loading || this.vibes.isEmpty()">loading..</div>
    <div v-else>
        <ul>
            <li v-for="notification in user.notifications">
                <div v-if="isRequestToJoinVibe(notification)">
                    <p>You have a join request from '{{ notification.data['user_display_name'] }}'.</p>
                </div>
                <div v-else-if="isRequestToJoinVibeAccepted(notification)">
                    <p>Your request to join '{{ vibes.getVibeName(notification.data['vibe_id'])}}' has been accepted.</p>
                </div>
                <div v-else-if="isRequestToJoinVibeRejected(notification)">
                    <p>Your request to join '{{ vibes.getVibeName(notification.data['vibe_id'])}}' has been rejected.</p>
                </div>
                <div v-else-if="userLeftVibe(notification)">
                    <p>'{{ notification.data.user_display_name }}' has left '{{ vibes.getVibeName(notification.data['vibe_id'])}}'.</p>
                </div>
                <div v-else-if="userJoinedVibe(notification)">
                    <p>'{{ notification.data.user_display_name }}' has joined '{{ vibes.getVibeName(notification.data['vibe_id'])}}'.</p>
                </div>
                <div v-else-if="isRemovedFromVibe(notification)">
                    <p>You have been removed from the '{{ vibes.getVibeName(notification.data['vibe_id']) }}' vibe.</p>
                </div>
            </li>
        </ul>
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
            user.getNotifications().then(() => this.loading = false);

            user.getID()
                .then(() => {
                    Echo.private('App.User.' + user.id)
                        .notification((notification) => {
                            user.notifications.unshift(notification.data);
                        });
                });
        },

        methods: {
            isRequestToJoinVibe(notification) {
                return notification.type === 'App\\Notifications\\RequestToJoinAVibe';
            },

            isRequestToJoinVibeAccepted(notification) {
                return notification.type === 'App\\Notifications\\RequestToJoinVibeAccepted';
            },

            isRequestToJoinVibeRejected(notification) {
                return notification.type === 'App\\Notifications\\RequestToJoinVibeRejected';
            },

            isRemovedFromVibe(notification) {
                return notification.type === 'App\\Notifications\\RemovedFromAVibe';
            },

            userLeftVibe(notification) {
                return notification.type === 'App\\Notifications\\LeftVibe';
            },

            userJoinedVibe(notification) {
                return notification.type === 'App\\Notifications\\JoinedVibe';
            },
        }
    }
</script>