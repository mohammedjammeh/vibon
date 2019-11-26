import VueRouter from 'vue-router';
import show from './components/vibe/show';

let routes = [
    {
        path: '/vibe/:id',
        name: 'showVibe',
        component: show
    }
];

export default new VueRouter({
    routes
});