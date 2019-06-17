<template>
    <div>
        <v-app class="inspire">
            <v-toolbar color="blue darken-3" dark tabs>
                <v-toolbar-title>
                    <a href="/">Proma</a>
                </v-toolbar-title>
                <v-spacer></v-spacer>

                <v-autocomplete
                    v-model="query"
                    :items="results"
                    :search-input.sync="searchInput"
                    item-text="title"
                    label="Search..."
                    prepend-inner-icon="search"
                    flat solo-inverted hide-details hide-no-data append-icon=""
                    class="hidden-sm-and-down"
                    @keyup="search"
                >
                    <template v-slot:item="{ item }">
                        <router-link :to="item.url" class="search-link">
                            <v-list-tile-content>
                                <v-list-tile-title>
                                    {{ item.title | truncate(50) }}
                                </v-list-tile-title>
                            </v-list-tile-content>
                        </router-link>
                    </template>
                </v-autocomplete>

                <v-spacer></v-spacer>
                <v-btn icon @click="getNotifications">
                    <v-icon>notifications</v-icon>
                </v-btn>
                <v-btn icon @click="getUser">
                    <v-icon>account_circle</v-icon>
                </v-btn>
                <template v-slot:extension>
                    <v-tabs v-model="tabs" centered color="blue darken-3" slider-color="white" fixed-tabs grow >
                        <v-tab to="/" >
                            <v-icon>work</v-icon>
                            <span class="tab-title">Projects</span>
                        </v-tab>
                        <v-tab to="/issues" >
                            <v-icon>bug_report</v-icon>
                            <span class="tab-title">Issues</span>
                        </v-tab>
                        <v-tab to="/clients" >
                            <v-icon>person</v-icon>
                            <span class="tab-title">Clients</span>
                        </v-tab>
                    </v-tabs>
                </template>
            </v-toolbar>

            <v-content>
                <router-view></router-view>

                <v-snackbar v-model="snackbar.enabled" :color="snackbar.color" :bottom="true" :right="true" :timeout="snackbar.timeout">
                    {{ snackbar.message }}
                    <v-btn color="white" flat @click="snackbar.enabled = false"><v-icon>close</v-icon></v-btn>
                </v-snackbar>
            </v-content>

            <v-dialog v-model="notifications.enabled" width="500">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Notifications</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <template v-for="(notification, index) in notifications.data">
                            <v-list :key="index">
                                <v-list-tile>
                                    <v-list-tile-content>
                                        <v-list-tile-title class="body-2">{{ notification.message }}</v-list-tile-title>
                                        <v-list-tile-subtitle class="caption grey--text">{{ notification.elapsed_time }}</v-list-tile-subtitle>
                                    </v-list-tile-content>
                                </v-list-tile>
                                <v-divider></v-divider>
                            </v-list>
                        </template>
                    </v-container>
                </v-card>
            </v-dialog>

            <v-dialog v-model="dialog" width="500">
                <v-form method="POST" id="userForm" @submit.prevent="updateUser">
                    <v-card>
                        <v-card-title class="blue darken-3 white--text py-4 title">My Account</v-card-title>
                        <v-container grid-list-sm class="pa-4">
                            <v-layout row wrap>
                                <v-flex xs12>
                                    <v-text-field prepend-icon="person" label="Name" v-model="user.name"></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field prepend-icon="email" label="Email" v-model="user.email"></v-text-field>
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

            <div class="footer text-xs-center mt-5 mb-3">
                Proma - Simple Project Management &copy; 2019
            </div>
        </v-app>
    </div>
</template>

<script>
import Event from './../events'

export default {
    name: 'App',
    data() {
        return {
            tabs: 'projects',
            dialog: false,
            query: '',
            searchInput: '',
            user: [],
            results: [],
            notifications: {
                enabled: false,
                data: ''
            },
            snackbar: {
                enabled: false,
                message: '',
                timeout: 5000,
                y: 'bottom',
                x: 'right',
                color: ''
            },
        }
    },
    methods: {
        search() {
            let query = this.searchInput

            if (!_.isNull(query) && query.length > 2) {
                axios.post('/api/search', { query })
                .then(response => {
                    this.results = response.data
                });
            }
        },
        getNotifications() {
            this.notifications.enabled = true

            axios.get('/api/notifications')
            .then(response => {
                this.notifications.data = response.data
                console.log('notifications', this.notifications.data)
            })
        },
        getUser() {
            this.dialog = true

            axios.get('/api/user')
            .then(response => {
                this.user = response.data
            })
        },
        updateUser() {
            let name = this.user.name;
            let email = this.user.email;

            axios.put('/api/user/', { name, email })
            .then(response => {
                this.snackbar.color = 'success'
                this.snackbar.message = "Account updated successfully!"
                this.snackbar.enabled = true
            })
            .catch(function (error) {
                this.snackbar.color = 'error'
                this.snackbar.message = "Error updating account!"
                this.snackbar.enabled = true
            })

            this.reset()
        },
        reset() {
            this.dialog = false
            this.name = ''
            this.description = ''
        },
        logout() {
            axios.get('/api/logout')
            .then(function () {
                location.reload()
            });
        }
    },
    created() {
        Event.$on('success', message => {
            this.snackbar.message = message
            this.snackbar.color = 'success'
            this.snackbar.enabled = true
        })
        Event.$on('warning', message => {
            this.snackbar.message = message
            this.snackbar.color = 'warning'
            this.snackbar.enabled = true
        })
        Event.$on('error', message => {
            this.snackbar.message = message
            this.snackbar.color = 'error'
            this.snackbar.enabled = true
        })
    }
}
</script>

<style lang="scss">
.container {
    padding-top: 6px !important;
    padding-bottom: 6px !important;
}

.clickable {
    cursor: pointer;
}

.data-card {
    cursor: pointer;

    &.small {
        height: 140px;
    }

    &.medium {
        height: 200px;
    }

    &.large {
        height: 245px;
    }
}

.search-link {
    height: 100% !important;
    width: 100% !important;
}

.v-card {
    padding-bottom: 10px;
}

</style>
