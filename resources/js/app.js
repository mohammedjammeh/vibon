import './bootstrap';
import router from './routes';
import vibes from './components/vibe/index';
import createVibe from './components/vibe/create';
import searchForm from './components/search/form';
import playback from './components/playback';

new Vue({
    el: '#app',
    components: {
        'vibes': vibes,
        'create-vibe': createVibe,
        'search-form': searchForm,
        'playback': playback
    },
    data: {},
    router: router
});
