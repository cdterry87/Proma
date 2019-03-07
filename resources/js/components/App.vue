<template>
    <v-app>
        <Home v-if="loggedIn" />
        <Login v-else />
    </v-app>
</template>

<script>
import eventBus from './../events';
import Login from './Login';
import Home from './Home';

export default {
    name: 'App',
    data () {
        return {
            loggedIn: false,
        }
    },
    components: {
        Login,
        Home
    },
    mounted() {
        eventBus.$on('login', userData => {
            this.loggedIn = false;
            if (!_.isEmpty(userData.jwt)) {
                this.loggedIn = true;
            }
        })
    }
}
</script>

<style>
a {
    color: inherit !important;
    text-decoration: none;
}

#copyright {
    text-align: center;
}
</style>
