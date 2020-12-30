import VueRouter from 'vue-router';
import show from './components/vibe/show';
import searchResults from './components/search/results';
import index from './components/index';

let routes = [
    {
        path: '/vibe/:id',
        name: 'showVibe',
        component: show
    },
    {
        path: '/search/:input',
        name: 'searchResults',
        component: searchResults
    },
    {
        path: '/',
        name: 'index',
        component: index
    }
];

export default new VueRouter({
    routes
});