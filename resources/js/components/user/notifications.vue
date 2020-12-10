<template>
    <div>
        <div v-if="this.notificationsIsEmpty()">
            <p>No notifications yet..</p>
        </div>

        <div v-else>
            <ul>
                <li v-for="notification in this.notifications">
                    <div v-if="isRequestToJoinVibe(notification)">
                        <p>You have a join request from '{{ notification.data['user_display_name'] }}'.</p>
                    </div>
                    <div v-else-if="isRequestToJoinVibeAccepted(notification)">
                        <p>Your request to join has been accepted.</p>
                    </div>
                    <div v-else-if="isRequestToJoinVibeRejected(notification)">
                        <p>Your request to join has been rejected.</p>
                    </div>
                    <div v-else-if="userLeftVibe(notification)">
                        <p>'{{ notification.data.user_display_name }}' has left.</p>
                    </div>
                    <div v-else-if="userJoinedVibe(notification)">
                        <p>'{{ notification.data.user_display_name }}' has joined.</p>
                    </div>
                    <div v-else-if="isRemovedFromVibe(notification)">
                        <p>You have been removed from this vibe by the owner.</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import user from '../../core/user.js';
    import vibes from '../../core/vibes.js';

    export default {
        props: ['notifications'],

        data() {
            return {
                user: user,
                vibes: vibes
            }
        },

        created() {
            user.getID()
                .then(() => {
                    Echo.private('App.User.' + user.id)
                        .notification((notification) => {
                            this.notifications.unshift(notification.data);
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

            notificationsIsEmpty() {
                if(this.notifications == null) {
                    return false;
                }
                return Object.keys(this.notifications).length === 0;
            },
        }
    }
</script>