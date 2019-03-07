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
                                    <v-form action="/login" method="POST" id="loginForm" @submit.prevent="formSubmit">
                                        <v-card flat>
                                            <v-card-text>
                                                <v-alert :value="loginErrors.length > 0" v-html="loginErrors" type="error"></v-alert>
                                                <br>
                                                <v-text-field prepend-icon="email" v-model="email" name="email" label="Email address" type="text"></v-text-field>
                                                <v-text-field prepend-icon="lock" v-model="password" name="password" label="Password" type="password" ></v-text-field>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn type="submit" color="primary" form="loginForm" @click="login">Login</v-btn>
                                                <v-spacer></v-spacer>
                                            </v-card-actions>
                                        </v-card>
                                    </v-form>
                                </v-tab-item>
                                <v-tab-item :value="'tab-register'">
                                    <v-form action="/register" method="POST" id="registerForm" @submit.prevent="formSubmit">
                                        <v-card flat>
                                            <v-card-text>
                                                <v-alert :value="registerErrors.length > 0" v-html="registerErrors" type="error"></v-alert>
                                                <br>
                                                <v-text-field prepend-icon="person" v-model="name" name="name" label="Full Name" type="text"></v-text-field>
                                                <v-text-field prepend-icon="email" v-model="email" name="email" label="Email address" type="text"></v-text-field>
                                                <v-text-field prepend-icon="lock" v-model="password" name="password" label="Password" type="password" ></v-text-field>
                                                <v-text-field prepend-icon="lock" v-model="password_confirmation" name="password_confirmation" label="Confirm Password" type="password" ></v-text-field>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn type="submit" color="primary" form="registerForm" @click="register">Register</v-btn>
                                                <v-spacer></v-spacer>
                                            </v-card-actions>
                                        </v-card>
                                    </v-form>
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
import eventBus from './../events';
import axios from 'axios';

export default {
    data: () => ({
        tabs: 'tab-login',
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        loginErrors: '',
        registerErrors: '',
    }),
    methods : {
        formSubmit() {
            console.log('form submit');
        },
        login(e) {
            e.preventDefault();
            if (this.email.length > 0 && this.password.length > 0) {
                var self = this;
                axios.post('api/login', {
                    email: this.email,
                    password: this.password
                })
                .then(response => {
                    localStorage.setItem('jwt',response.data.success.token)

                    let userData = {
                        jwt: response.data.success.token,
                    }

                    if (localStorage.getItem('jwt') != null){
                        eventBus.$emit('login', userData);
                    }
                })
                .catch(function (error) {
                    console.log(error);
                    self.loginErrors = error.response.data.error;
                });
            } else {
                this.loginErrors = 'You must enter a username and password.';
            }
        },
        register(e) {
            e.preventDefault()
            if (this.password.length > 0 && this.password === this.password_confirmation && this.password.length > 0) {
                var self = this;
                axios.post('api/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                })
                .then(response => {
                    localStorage.setItem('jwt',response.data.success.token)

                    let userData = {
                        jwt: response.data.success.token,
                    }

                    if (localStorage.getItem('jwt') != null){
                        eventBus.$emit('login', userData);
                    }
                })
                .catch(error => {
                    console.log(error);
                    self.registerErrors = error.response.data.error;
                });
            } else {
                this.password = ""
                this.password_confirmation = ""

                this.registerErrors = 'Passwords do not match!';
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
