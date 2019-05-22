<template>
    <div>
        <v-container fluid grid-list-md>
            <v-layout align-baseline>
                <v-flex xs10>
                    <span class="headline">
                        {{ client.name }}
                    </span>
                </v-flex>
                <v-flex xs2 text-xs-right>
                    <v-btn color="info" @click="dialog = true" small>
                        <v-icon left dark>edit</v-icon>
                        Edit
                    </v-btn>
                </v-flex>
            </v-layout>
            <v-layout row>
                <v-flex xs12>
                    {{ client.description }}
                </v-flex>
            </v-layout>
        </v-container>

        <ClientContacts :clientInfo="client" :clientContacts="contacts" />

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="editClientForm" @submit.prevent="updateClient">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Client</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="business" label="Client Name" v-model="client.name" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="client.description"></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" color="blue darken-2" flat>Save</v-btn>
                        <v-btn flat color="red darken-2" form="editClientForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </div>
</template>

<script>
    import EventBus from './../eventbus'
    import ClientContacts from './ClientContacts'

    export default {
        name: 'Client',
        props: ['id'],
        components: {
            ClientContacts
        },
        data() {
            return {
                dialog: false,
                client: '',
                contacts: ''
            }
        },
        methods: {
            getClient() {
                axios.get('/api/clients/' + this.id)
                .then(response => {
                    this.client = response.data

                    this.getClientContacts(this.id);
                })
            },
            getClientContacts(client_id) {
                axios.get('/api/contacts/' + client_id)
                .then(response => {
                    this.contacts = response.data
                })
            },
            updateClient() {
                let name = this.client.name;
                let description = this.client.description;

                axios.put('/api/clients/' + this.client.id, { name, description })
                .then(response => {
                    // this.client = response.data.data
                })

                this.reset()
            },
            reset() {
                this.dialog = false
                this.name = ''
                this.description = ''
            }
        },
        created() {
            EventBus.$on('addContact', contacts => {
                this.contacts = contacts
            })

            EventBus.$on('loadContacts', client_id => {
                this.getClientContacts(client_id)
            })
        },
        mounted() {
            this.getClient()
        }
    }
</script>
