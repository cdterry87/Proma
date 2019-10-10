<template>
    <v-app id="inspire" dark>
        <v-container fluid fill-height>
            <v-layout align-center justify-center wrap>
                <v-flex xs12 sm10 md5>
                    <div class="text-xs-center mb-3">
                        <h1 class="display-1 logo">Proma</h1>
                        <h2 class="subheading mt-2">Fill out your email address to send a password reset link.</h2>
                    </div>
                    <v-text-field color="white" label="Email" box prepend-inner-icon="mail" id="email" name="email" type="text"></v-text-field>
                    <div class="text-xs-center">
                        <v-btn outline flat type="submit">Send Password Reset</v-btn>
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
    </v-app>
</template>

<script>
    export default {
        name: 'Email',
        props: ['title']
    }
</script>
