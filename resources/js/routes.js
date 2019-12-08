import VueRouter from 'vue-router';
import show from './components/vibe/show';
import searchResults from './components/search/results';

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
    }
];

export default new VueRouter({
    routes
});