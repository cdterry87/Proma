<template>
    <v-app>
        <v-content>
            <v-container fluid fill-height>
                <v-layout align-center justify-center>
                    <v-flex xs12 sm8 md5>
                        <v-card>
                            <v-toolbar color="primary" dark>
                                <v-toolbar-title>Proma</v-toolbar-title>
                            </v-toolbar>
                            <v-tabs v-model="tabs" fixed-tabs centered slider-color="primary" >
                                <v-tab :href="'#tab-login'">Login</v-tab>
                                <v-tab :href="'#tab-register'">Register</v-tab>
                            </v-tabs>
                            <v-tabs-items v-model="tabs">
                                <v-tab-item :value="'tab-login'">
                                    <v-card flat>
                                        <v-card-text>
                                            <v-form action="/login" method="POST">
                                                <v-text-field prepend-icon="email" v-model="email" name="email" label="Email address" type="text"></v-text-field>
                                                <v-text-field prepend-icon="lock" v-model="password" name="password" label="Password" type="password" ></v-text-field>
                                            </v-form>
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn color="primary" @click="login">Login</v-btn>
                                            <v-spacer></v-spacer>
                                        </v-card-actions>
                                    </v-card>
                                </v-tab-item>
                                <v-tab-item :value="'tab-register'">
                                    <v-card flat>
                                        <v-card-text>
                                            <v-card-text>
                                                <v-form action="/login" method="POST">
                                                    <v-text-field prepend-icon="person" v-model="name" name="name" label="Full Name" type="text"></v-text-field>
                                                    <v-text-field prepend-icon="email" v-model="email" name="email" label="Email address" type="text"></v-text-field>
                                                    <v-text-field prepend-icon="lock" v-model="password" name="password" label="Password" type="password" ></v-text-field>
                                                    <v-text-field prepend-icon="lock" v-model="password_confirmation" name="password_confirmation" label="Confirm Password" type="password" ></v-text-field>
                                                </v-form>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="primary"  @click="register">Register</v-btn>
                                                <v-spacer></v-spacer>
                                            </v-card-actions>
                                        </v-card-text>
                                    </v-card>
                                </v-tab-item>
                            </v-tabs-items>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
import axios from 'axios';

export default {
    data: () => ({
        tabs: 'tab-login',
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    }),
    methods : {
        login(e) {
            e.preventDefault();
            if (this.password.length > 0) {
                // Need to install laravel passport and setup User model
                axios.post('api/login', {
                    email: this.email,
                    password: this.password
                })
                .then(response => {
                    localStorage.setItem('user',response.data.success.name)
                    localStorage.setItem('jwt',response.data.success.token)

                    if (localStorage.getItem('jwt') != null){
                        // emit event to set loggedIn = true
                    }
                })
                .catch(function (error) {
                    console.error(error);
                });
            }
        },
        register(e) {
            e.preventDefault()
            if (this.password === this.password_confirmation && this.password.length > 0) {
                axios.post('api/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                })
                .then(response => {
                    localStorage.setItem('user',response.data.success.name)
                    localStorage.setItem('jwt',response.data.success.token)

                    if (localStorage.getItem('jwt') != null){
                        // emit an event to set loggedIn = true
                    }
                })
                .catch(error => {
                    console.error(error);
                });
            } else {
                this.password = ""
                this.password_confirmation = ""

                return alert('Passwords do not match')
            }
        }
    },
    beforeRouteEnter (to, from, next) {
        if (localStorage.getItem('jwt')) {
            // emit event to set loggedIn = true
        }
        next();
    }
}
</script>
