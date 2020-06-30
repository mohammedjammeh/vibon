import './bootstrap';
import './listeners';
import router from './routes';
import vibes from './components/vibe/index';
import createVibe from './components/vibe/create';
import searchForm from './components/search/form';
import playback from './components/playback';
import userNotifications from './components/user/notifications';

new Vue({
    el: '#app',
    components: {
        'vibes': vibes,
        'create-vibe': createVibe,
        'search-form': searchForm,
        'playback': playback,
        'user-notifications': userNotifications
    },
    data: {},
    router: router
});
