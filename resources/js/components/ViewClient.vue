<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container>
                <v-layout align-baseline>
                    <v-flex xs10>
                        <span class="headline">
                            {{ client.name }}
                        </span>
                    </v-flex>
                    <v-flex xs2 text-xs-right>
                        <v-btn color="info" @click="editClient" small>
                            <v-icon left dark>edit</v-icon>
                            Edit
                        </v-btn>
                    </v-flex>
                </v-layout>
            </v-container>
            <v-container>
                <v-layout row>
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
    import EventBus from './../eventbus';

    export default {
        name: 'ViewClient',
        props: ['clientInfo'],
        methods: {
            editClient() {
                let editClient = true
                EventBus.$emit('editClient', editClient);
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
