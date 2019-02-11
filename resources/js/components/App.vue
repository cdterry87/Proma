<template>
    <v-app>
        <v-toolbar app color="indigo darken-3" text-color="white" id="toolbar">
            <router-link :to="'/'"><v-toolbar-title v-text="title"></v-toolbar-title></router-link>
        </v-toolbar>
        <v-content>
            <v-container fluid>
                <router-view/>

                <div id="copyright">Chase Terry &copy; 2019</div>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
export default {
    name: 'App',
    data () {
        return {
            title: 'Proma'
        }
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
