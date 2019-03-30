<template>
    <v-app>
        <Home v-if="loggedIn" :userData="userData" />
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
            userData: null
        }
    },
    components: {
        Login,
        Home
    },
    created() {
        eventBus.$on('login', userData => {
            this.loggedIn = false;
            if (!_.isEmpty(userData.jwt)) {
                this.loggedIn = true;
                this.userData = userData;
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

.container {
    padding-top: 6px !important;
    padding-bottom: 6px !important;
}

.editCard {
    cursor: pointer;
}

.v-card {
    padding-bottom: 10px;
}

#copyright {
    text-align: center;
}
</style>
