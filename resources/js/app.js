
import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import Vuetify from 'vuetify'
Vue.use(Vuetify)

import App from './components/App'
import Teams from './components/Teams'
import Team from './components/Team'
import Clients from './components/Clients'
import Client from './components/Client'
import Projects from './components/Projects'
import Project from './components/Project'

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
            path: '/team/:id',
            name: 'team',
            component: Team,
            props: true
        },
        {
            path: '/clients',
            name: 'clients',
            component: Clients,
        },
        {
            path: '/client/:id',
            name: 'client',
            component: Client,
            props: true
        },
        {
            path: '/project/:id',
            name: 'project',
            component: Project,
            props: true
        },
    ],
});

Vue.filter('truncate', function (string, length) {
    if (!string) return ''
    string = string.toString()
    return _.truncate(string, { length })
})

const app = new Vue({
    el: '#app',
    components: { App },
    router,
});
