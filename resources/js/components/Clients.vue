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
            <v-form method="POST" id="clientForm" @submit.prevent="addClient" ref="form" lazy-validation>
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Client</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="business" label="Client Name" v-model="name" maxlength="100" :rules="[v => !!v || 'Name is required']" required></v-text-field>
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
    </v-container>
</template>

<script>
import Event from './../events'

export default {
    name: 'Clients',
    data() {
        return {
            dialog: false,
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
            if (this.$refs.form.validate()) {
                let name = this.name
                let description = this.description

                axios.post('/api/clients', { name, description })
                .then(response => {
                    this.getClients()

                    Event.$emit('success', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })

                this.reset()
            }
        },
        deleteClient(client_id) {
            axios.delete('/api/clients/' + client_id)
            .then(response => {
                this.getClients()

                Event.$emit('success', response.data.message)
            })
            .catch(function (error) {
                Event.$emit('error', response.data.message)
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
