
import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import Vuetify from 'vuetify'
Vue.use(Vuetify)

import App from './components/App'
import Login from './components/Login'
import Register from './components/Register'
import Home from './components/Home'
import Team from './components/Team'
import Client from './components/Client'
import Project from './components/Project'

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
        },
        {
            path: '/project',
            name: 'project',
            component: Project,
        },
        {
            path: '/team',
            name: 'team',
            component: Team,
        },
        {
            path: '/client',
            name: 'client',
            component: Client,
        },
    ],
});

const app = new Vue({
    el: '#app',
    components: { App },
    router,
});
