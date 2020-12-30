<template>
    <div>
        <h4>Notifications</h4>
        <div v-if="this.notificationsIsEmpty()">
            <p>No notifications yet..</p>
        </div>

        <div v-else>
            <ul>
                <li v-for="notification in this.vibes.show.notifications">
                    <div v-if="isRequestToJoinVibeAccepted(notification)">
                        <p>Join request has been accepted.</p>
                    </div>
                    <div v-else-if="isRequestToJoinVibeRejected(notification)">
                        <p>Join request has been rejected.</p>
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

        <br><br>
    </div>
</template>

<script>
    import user from '../../core/user.js';
    import vibes from '../../core/vibes.js';

    export default {
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
                            this.vibes.show.notifications.unshift(notification.data);
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
                if(this.vibes.show.notifications == null) {
                    return false;
                }
                return Object.keys(this.vibes.show.notifications).length === 0;
            },
        }
    }
</script>

<style scoped>
    ul {
        padding: 0;
        margin: 0;
        list-style-type: none;
    }
</style>