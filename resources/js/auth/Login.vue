<template>
    <v-app id="inspire" dark>
        <v-content>
            <v-container fluid fill-height>
                <v-layout align-center justify-center wrap>
                    <v-flex xs12 sm10 md5>
                        <div class="text-xs-center mb-3">
                            <h1 class="display-1">Proma</h1>
                            <h2 class="subheading mt-2">Sign in to start managing your projects!</h2>
                        </div>
                        <v-text-field color="white" label="Email" box prepend-inner-icon="mail" id="email" name="email" type="text"></v-text-field>
                        <v-text-field color="white" label="Password" box prepend-inner-icon="lock" id="password" name="password" type="password"></v-text-field>
                        <v-checkbox color="white" id="remember" name="remember" label="Remember Me"></v-checkbox>
                        <div class="text-xs-center">
                            <v-btn outline flat type="submit" color="white">Login</v-btn>
                            <v-btn flat href="/register">Register</v-btn>
                        </div>
                        <div class="text-xs-center mt-3">
                            <v-btn flat href="/password/reset">Forgot your password?</v-btn>
                        </div>
                        <v-container>
                            <v-layout align-center class="mt-2">
                                <v-flex xs12>
                                    <v-divider></v-divider>
                                    <div class="mt-4 text-xs-center">
                                        &copy; Proma 2019. All Rights Reserved.
                                    </div>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
    export default {
        name: 'Login'
    }
</script>
