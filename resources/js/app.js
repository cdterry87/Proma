
import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import Vuetify from 'vuetify'
Vue.use(Vuetify)

import App from './components/App'
import Teams from './components/Teams'
import Clients from './components/Clients'
import Projects from './components/Projects'

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Projects,
        },
        {
            path: '/teams',
            name: 'teams',
            component: Teams,
        },
        {
            path: '/clients',
            name: 'clients',
            component: Clients,
        },
    ],
});

const app = new Vue({
    el: '#app',
    components: { App },
    router,
});
