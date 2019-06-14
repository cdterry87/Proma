<template>
    <v-container fluid grid-list-md>
        <v-layout row>
            <v-container text-xs-center>
                <v-btn color="info" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Add a Client
                </v-btn>
            </v-container>
        </v-layout>
        <v-layout row text-xs-center>
            <v-container v-if="clients.length == 0" class="headline">
                You do not currently have any clients.
            </v-container>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12 md6 lg4 v-for="client in clients" :key="client.id">
                    <v-card class="data-card medium">
                        <router-link :to="'/client/' + client.id">
                            <v-card-text>
                                <div class="headline">
                                    {{ client.name | truncate(25) }}
                                </div>
                                {{ client.description | truncate(200) }}
                            </v-card-text>
                        </router-link>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn flat color="red darken-2" @click="removeClient(client.id)">Remove</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="clientForm" @submit.prevent="addClient">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Client</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="business" label="Client Name" v-model="name" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="description"></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat color="blue darken-2">Save</v-btn>
                        <v-btn flat color="red darken-2" form="clientForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>

        <v-snackbar v-model="snackbar.enabled" :color="snackbar.color" :bottom="true" :right="true" :timeout="snackbar.timeout">
            {{ snackbar.message }}
            <v-btn color="white" flat @click="snackbar.enabled = false"><v-icon>close</v-icon></v-btn>
        </v-snackbar>
    </v-container>
</template>

<script>
export default {
    name: 'Clients',
    data() {
        return {
            dialog: false,
            snackbar: {
                enabled: false,
                message: '',
                timeout: 5000,
                y: 'bottom',
                x: 'right',
                color: ''
            },
            name: '',
            description: '',
            clients: []
        }
    },
    methods: {
        getClients() {
            axios.get('/api/clients')
            .then(response => {
                this.clients = response.data
            })
        },
        addClient() {
            let name = this.name
            let description = this.description

            axios.post('/api/clients', { name, description })
            .then(response => {
                this.clients.push(response.data.data)

                this.snackbar.color = 'success'
                this.snackbar.message = "Client successfully created!"
                this.snackbar.enabled = true
            })
            .catch(function (error) {
                this.snackbar.color = 'error'
                this.snackbar.message = "Error creating client!"
                this.snackbar.enabled = true
            })

            this.reset()
        },
        removeClient(client_id) {
            axios.delete('/api/clients/' + client_id)
            .then(response => {
                this.getClients()

                this.snackbar.color = 'success'
                this.snackbar.message = "Client successfully removed!"
                this.snackbar.enabled = true
            })
        },
        reset() {
            this.dialog = false
            this.name = ''
            this.description = ''
        }
    },
    mounted() {
        this.getClients()
    }

}
</script>
