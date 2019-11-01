import './bootstrap';
import router from './routes';
import vibes from './components/vibe/index';
import createVibe from './components/vibe/create';

new Vue({
    el: '#app',
    components: {
        'vibes': vibes,
        'create-vibe': createVibe
    },
    data: {},
    router: router
});
