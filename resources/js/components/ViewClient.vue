<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container text-xs-right>
                <v-btn color="info" @click="editClient">
                    <v-icon left dark>edit</v-icon>
                    Edit Client
                </v-btn>
            </v-container>
            <v-container>
                <v-layout row>
                    <v-flex xs12 class="headline">
                        {{ client.name }}
                    </v-flex>
                </v-layout>
                <v-layout row>
                    <v-flex xs12>
                        {{ client.description }}
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
    </v-container>
</template>

<script>
    import eventBus from './../events';

    export default {
        name: 'ViewClient',
        props: ['clientInfo'],
        methods: {
            editClient() {
                let editClient = true
                eventBus.$emit('editClient', editClient);
            }
        },
        computed: {
            client() {
                return this.clientInfo
            }
        }
    }
</script>

<style scoped>

</style>
