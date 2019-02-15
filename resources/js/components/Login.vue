<template>
    <v-app>
        <v-content>
            <v-container fluid fill-height>
                <v-layout align-center justify-center>
                    <v-flex xs12 sm8 md4>
                        <v-card class="elevation-12">
                            <v-toolbar dark color="primary">
                                <v-toolbar-title>Proma - Login</v-toolbar-title>
                            </v-toolbar>
                            <v-card-text>
                                <v-form action="/login" method="POST">
                                    <v-text-field prepend-icon="email" name="email" label="Email address" type="text"></v-text-field>
                                    <v-text-field
                                        id="password"
                                        prepend-icon="lock"
                                        name="password"
                                        label="Password"
                                        type="password"
                                    ></v-text-field>
                                </v-form>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="primary">Login</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
export default {
    name: 'Login',
    data(){
        return {
            email : "",
            password : "",
        }
    },
    methods : {
        login(e){
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
                        this.$router.go('/board')
                    }
                })
                .catch(function (error) {
                    console.error(error);
                });
            }
        }
    },
    beforeRouteEnter (to, from, next) {
        if (localStorage.getItem('jwt')) {
            return next('board');
        }
        next();
    }
};
</script>
