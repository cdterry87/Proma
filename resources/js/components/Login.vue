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
                                                <v-text-field prepend-icon="email" name="email" label="Email address" type="text"></v-text-field>
                                                <v-text-field id="password" prepend-icon="lock" name="password" label="Password" type="password" ></v-text-field>
                                            </v-form>
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn color="primary">Login</v-btn>
                                            <v-spacer></v-spacer>
                                        </v-card-actions>
                                    </v-card>
                                </v-tab-item>
                                <v-tab-item :value="'tab-register'">
                                    <v-card flat>
                                        <v-card-text>
                                            <v-card-text>
                                                <v-form action="/login" method="POST">
                                                    <v-text-field prepend-icon="person" name="name" label="Full Name" type="text"></v-text-field>
                                                    <v-text-field prepend-icon="email" name="email" label="Email address" type="text"></v-text-field>
                                                    <v-text-field prepend-icon="lock" name="password" label="Password" type="password" ></v-text-field>
                                                    <v-text-field prepend-icon="lock" name="password_confirmation" label="Confirm Password" type="password" ></v-text-field>
                                                </v-form>
                                            </v-card-text>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn color="primary">Register</v-btn>
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
                axios.post('api/login', {
                    email: this.email,
                    password: this.password
                })
                .then(response => {
                    localStorage.setItem('user',response.data.success.name)
                    localStorage.setItem('jwt',response.data.success.token)

                    if (localStorage.getItem('jwt') != null){
                        this.$router.go('home')
                    }
                })
                .catch(function (error) {
                    console.error(error);
                });
            }
        },
        register(e) {

        }
    },
    beforeRouteEnter (to, from, next) {
        if (localStorage.getItem('jwt')) {
            return next('home');
        }
        next();
    }
}
</script>
