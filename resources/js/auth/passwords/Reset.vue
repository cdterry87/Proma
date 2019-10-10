<template>
    <v-app id="inspire" dark>
        <v-content>
            <v-container fluid fill-height>
                <v-layout align-center justify-center wrap>
                    <v-flex xs12 sm10 md5>
                        <div class="text-xs-center mb-3">
                            <h1 class="display-1 logo">Proma</h1>
                            <h2 class="subheading mt-2">Reset your password using the form below.</h2>
                        </div>
                        <v-text-field prepend-icon="lock" id="password" name="password" label="Password" type="password"></v-text-field>
                        <v-text-field prepend-icon="lock" id="password_confirmation"  name="password_confirmation" label="Confirm Password" type="password"></v-text-field>
                        <div class="text-xs-center">
                            <v-btn outline flat type="submit">Reset Password</v-btn>
                            <v-btn flat href="/login">Login</v-btn>
                        </div>
                        <v-container>
                            <v-layout align-center class="mt-2">
                                <v-flex xs12>
                                    <v-divider></v-divider>
                                    <div class="mt-4 text-xs-center">
                                        &copy; {{ title }} {{ new Date().getFullYear()}}
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
        name: 'Reset',
        props: ['title']
    }
</script>
