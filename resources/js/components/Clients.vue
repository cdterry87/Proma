<template>
    <v-container fluid grid-list-md>
        <v-layout row>
            <v-container text-xs-center>
                <v-btn color="primary" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Add a Client
                </v-btn>
            </v-container>
        </v-layout>

        <v-layout row>
            <v-container>
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                    box
                ></v-text-field>
                <v-data-table
                    :headers="headers"
                    :items="clients"
                    :search="search"
                    :pagination.sync="pagination"
                    hide-actions
                    class="elevation-1"
                    no-data-text="You do not currently have any clients."
                >
                    <template v-slot:items="props">
                        <td>{{ props.item.name }}</td>
                        <td width="25%">
                            <v-form method="POST" id="deleteForm" @submit.prevent="deleteClient(props.item.id)">
                                <v-btn small :to="'/client/' + props.item.id" color="primary" class="white--text">View</v-btn>
                                <v-btn small type="submit" color="red darken-1" class="white--text">Delete</v-btn>
                            </v-form>
                        </td>
                    </template>
                </v-data-table>
            </v-container>
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
            clients: [],
            search: '',
            pagination: {
                sortBy: 'name',
                rowsPerPage: -1
            },
            headers: [
                { text: 'Client', value: 'name' },
                { text: 'Actions', value: 'actions', sortable: false },
            ],
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
        deleteClient(client_id) {
            axios.delete('/api/clients/' + client_id)
            .then(response => {
                this.getClients()

                this.snackbar.color = 'success'
                this.snackbar.message = "Client successfully deleted!"
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
