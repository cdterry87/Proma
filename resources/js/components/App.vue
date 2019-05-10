<template>
    <div id="app">
        <v-app class="inspire">
            <v-toolbar color="blue darken-3" dark tabs >
                <v-toolbar-title>
                    <a href="/">Proma</a>
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-text-field flat solo-inverted hide-details prepend-inner-icon="search" label="Search" class="hidden-sm-and-down" ></v-text-field>
                <v-spacer></v-spacer>
                <v-btn icon>
                    <v-icon>notifications</v-icon>
                </v-btn>
                <v-btn icon @click="dialog = true">
                    <v-icon>account_circle</v-icon>
                </v-btn>
                <template v-slot:extension>
                    <v-tabs v-model="tabs" centered color="blue darken-3" slider-color="white" fixed-tabs grow >
                        <v-tab to="/" >
                            <v-icon>work</v-icon>
                            <span class="tab-title">Projects</span>
                        </v-tab>
                        <v-tab to="/clients" >
                            <v-icon>person</v-icon>
                            <span class="tab-title">Clients</span>
                        </v-tab>
                        <v-tab to="/teams" >
                            <v-icon>people</v-icon>
                            <span class="tab-title">Teams</span>
                        </v-tab>
                    </v-tabs>
                </template>
            </v-toolbar>

            <v-content>
                <router-view></router-view>
            </v-content>

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
                            <v-btn flat color="primary" type="submit">Save</v-btn>
                            <v-btn flat color="primary" form="clientForm" @click="dialog = false">Cancel</v-btn>
                            <v-btn flat color="red" form="clientForm" @click="logout">Sign Out</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-form>
            </v-dialog>
        </v-app>
    </div>
</template>

<script>
export default {
    name: 'App',
    data() {
        return {
            tabs: 'projects',
            dialog: false,
            name: '',
            email: '',
        }
    },
    methods: {
        logout() {
            axios.post('logout')
            .then(response => {
                location.reload()
            })
        }
    }
}
</script>
