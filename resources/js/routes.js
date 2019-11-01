import VueRouter from 'vue-router';
import showVibe from './components/vibe/show';

let routes = [
    {
        path: '/vibe/:id',
        name: 'showVibe',
        component: showVibe
    }
];

export default new VueRouter({
    routes
});