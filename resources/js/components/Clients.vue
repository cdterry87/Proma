<template>
    <v-container fluid fill-height>
        <v-layout justify-center align-center>
            Welcome to the Clients Page

            <v-btn color="info" @click="dialog = true">
                <v-icon left dark>add</v-icon>
                Add a client
            </v-btn>
        </v-layout>

        <ClientCreate :dialog="dialog" />
    </v-container>
</template>

<script>
import ClientCreate from './Client/Create';

export default {
    name: 'Clients',
    components: {
        ClientCreate
    },
    data() {
        return {
            dialog: false,
        }
    }
}
</script>
