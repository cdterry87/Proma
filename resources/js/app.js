// Vue import
import Vue from 'vue'

// Vuetify settings
import Vuetify from 'vuetify'
Vue.use(Vuetify)

// Router settings
import VueRouter from 'vue-router'
import routes from './routes'

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes,
})

// Filters
Vue.filter('truncate', function (string, length) {
    if (!string) return ''
    string = string.toString()
    return _.truncate(string, { length })
})

Vue.filter('fromNow', function (date, format) {
    if (!date) return ''

    if (_.isEmpty(format)) {
        format = 'YYYY-MM-DD hh:mm:ss'
    }

    return moment(date, format).fromNow()
})

//Primary components
import App from './components/App'

// App declaration
const app = new Vue({
    el: '#app',
    components: { App },
    router,
});
