<template>
    <v-app>
        <Login v-if="!loggedIn" />
        <div v-else>
            <v-toolbar app dark color="primary" text-color="white" id="toolbar">
                <router-link :to="'/'"><v-toolbar-title v-text="title"></v-toolbar-title></router-link>
            </v-toolbar>
            <v-content>
                <v-container fluid>
                    <router-view/>

                    <div id="copyright">Chase Terry &copy; 2019</div>
                </v-container>
            </v-content>
        </div>
    </v-app>
</template>

<script>
import Login from './Login';

export default {
    name: 'App',
    data () {
        return {
            title: 'Proma',
            loggedIn: false,
        }
    },
    components: {
        Login
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
