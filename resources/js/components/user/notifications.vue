<template>
    <div v-if="this.vibes.show.currentUserIsAMember">
        <h4>Notifications</h4>
        <div v-if="notificationsIsEmpty">
            <p>No notifications yet..</p>
        </div>

        <div v-else>
            <ul>
                <li v-for="notification in this.vibes.show.notifications">
                    <div v-if="isRequestToJoinVibe(notification)">
                        <p>'{{ notification.data.user_display_name }}' sent join request.</p>
                    </div>
                    <div v-else-if="isRequestToJoinVibeAccepted(notification)">
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
                    <div v-else-if="userRemovedFromVibe(notification)">
                        <p>You have been removed from this vibe by the owner.</p>
                    </div>
                    <div v-else-if="pendingAttachVibeTracksAccepted(notification)">
                        <p> You request to add '{{ notification.data.track_name }}' track has been accepted.</p>
                    </div>
                    <div v-else-if="pendingAttachVibeTracksRejected(notification)">
                        <p> You request to add '{{ notification.data.track_name }}' track has been rejected.</p>
                    </div>
                    <div v-else-if="pendingDetachVibeTracksAccepted(notification)">
                        <p> You request to remove '{{ notification.data.track_name }}' track has been accepted.</p>
                    </div>
                    <div v-else-if="pendingDetachVibeTracksRejected(notification)">
                        <p> You request to remove '{{ notification.data.track_name }}' track has been rejected.</p>
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
            Echo.private('App.User.' + user.id)
                .notification((notification) => {
                    for(let key in this.vibes.show.notifications) {
                        if(this.vibes.show.notifications.hasOwnProperty(key)) {
                            this.vibes.show.notifications[parseInt(key) + 1] = this.vibes.show.notifications[key];
                        }
                    }
                    this.vibes.show.notifications[0] = notification;
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

            userLeftVibe(notification) {
                return notification.type === 'App\\Notifications\\LeftVibe';
            },

            userJoinedVibe(notification) {
                return notification.type === 'App\\Notifications\\JoinedVibe';
            },

            userRemovedFromVibe(notification) {
                return notification.type === 'App\\Notifications\\RemovedFromAVibe';
            },

            pendingAttachVibeTracksAccepted(notification) {
                return notification.type === 'App\\Notifications\\PendingAttachVibeTracksAcceptedNotification';
            },

            pendingAttachVibeTracksRejected(notification) {
                return notification.type === 'App\\Notifications\\PendingAttachVibeTracksRejectedNotification';
            },

            pendingDetachVibeTracksAccepted(notification) {
                return notification.type === 'App\\Notifications\\PendingDetachVibeTracksAcceptedNotification';
            },

            pendingDetachVibeTracksRejected(notification) {
                return notification.type === 'App\\Notifications\\PendingDetachVibeTracksRejectedNotification';
            },
        },

        computed: {
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