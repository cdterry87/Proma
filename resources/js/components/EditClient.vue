<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container text-xs-right>
                <v-btn color="info" @click="viewClient" small>
                    <v-icon left dark>remove_red_eye</v-icon>
                    View
                </v-btn>
            </v-container>
            <v-container>
                <v-form method="POST" id="editClientForm" @submit.prevent="updateClient">
                    <v-layout row>
                        <v-flex xs12>
                            <v-text-field prepend-icon="person" label="Client Name" v-model="client.name" maxlength="100"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex xs12>
                            <v-textarea prepend-icon="notes" label="Description" v-model="client.description"></v-textarea>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat>Save</v-btn>
                        <v-spacer></v-spacer>
                    </v-layout>
                </v-form>
            </v-container>

        </v-card>
    </v-container>
</template>

<script>
    import EventBus from './../eventbus';

    export default {
        name: 'EditClient',
        props: ['clientInfo'],
        methods: {
            viewClient() {
                let editClient = false
                EventBus.$emit('editClient', editClient);
            },
            updateClient() {
                let name = this.client.name;
                let description = this.client.description;

                axios.put('/api/clients/' + this.client.id, { name, description })
                .then(response => {
                    this.client = response.data.data
                })
            },
        },
        computed: {
            client: {
                get() {
                    return this.clientInfo
                },
                set(value) {
                    return value;
                }
            }
        },
    }
</script>

<style scoped>

</style>
