<template>
    <div>
        <v-navigation-drawer v-model="drawer" :clipped="$vuetify.breakpoint.lgAndUp" fixed app>
            <v-list dense>
                <template v-for="item in items">
                    <v-list-tile :key="item.text" @click>
                        <v-list-tile-action>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                <router-link :to="{ name: item.route }">{{ item.text }}</router-link>
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </template>
            </v-list>
        </v-navigation-drawer>
        <v-toolbar app fixed clipped-left dark color="blue darken-3">
            <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
            <v-toolbar-title>Proma</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-text-field flat solo-inverted hide-details prepend-inner-icon="search" label="Search" class="hidden-sm-and-down" ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn icon>
                <v-icon>notifications</v-icon>
            </v-btn>
            <v-btn icon @click="dialog = true">
                <v-icon>account_circle</v-icon>
            </v-btn>
        </v-toolbar>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="userForm" @submit.prevent="updateUser">
                <v-card>
                    <v-card-title class="grey lighten-4 py-4 title">My Account</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="person" label="Name" v-model="name"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field prepend-icon="email" label="Email" v-model="email"></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat>Save</v-btn>
                        <v-btn flat color="primary" form="clientForm" @click="dialog = false">Cancel</v-btn>
                        <v-btn flat color="red" form="clientForm" @click="logout">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name: 'Navigation',
    data() {
        return {
            drawer: null,
            dialog: false,
            name: '',
            email: '',
            items: [
                { icon: 'home', text: 'Home', route: 'home' },
                { icon: 'person', text: 'Clients', route: 'clients' },
                { icon: 'people', text: 'Teams', route: 'teams' },
            ]
        }
    },
    methods: {
        logout() {

        }
    }
}
</script>

<style scoped>
    aside a {
        height: 100%;
        width: 100%;
        display: block;
    }
</style>
