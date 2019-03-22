
import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

import Vuetify from 'vuetify'
Vue.use(Vuetify)

import App from './components/App'
import Teams from './components/Teams'
import EditTeam from './components/EditTeam'
import Clients from './components/Clients'
import EditClient from './components/EditClient'
import Projects from './components/Projects'
import EditProject from './components/EditProject'

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
            component: EditTeam,
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
            component: EditClient,
            props: true
        },
        {
            path: '/project/:id',
            name: 'project',
            component: EditProject,
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
