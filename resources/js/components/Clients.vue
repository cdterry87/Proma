<template>
    <v-container fluid fill-height>
        <v-layout justify-center align-center>
            Welcome to the Clients Page

            <v-btn color="info" @click="dialog = true">
                <v-icon left dark>add</v-icon>
                Add a client
            </v-btn>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-card>
                <v-card-title class="grey lighten-4 py-4 title">Create Client</v-card-title>
                <v-container grid-list-sm class="pa-4">
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-text-field prepend-icon="business" label="Client Name" v-model="name"></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-textarea prepend-icon="notes" label="Description" v-model="description"></v-textarea>
                        </v-flex>
                    </v-layout>
                </v-container>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn flat @click="create">Save</v-btn>
                    <v-btn flat color="primary" @click="dialog = false">Cancel</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script>
export default {
    name: 'Clients',
    data() {
        return {
            dialog: false,
            name: '',
            description: '',
            clients: []
        }
    },
    methods: {
        create() {
            let user_id = 1;
            let name = this.name;
            let description = this.description;

            axios.post('api/clients', { user_id, name, description } )
            .then(response => {
                this.clients.push(response.data.data)
            })

            this.dialog = false;
        }
    }
}
</script>
