<template>
    <v-container fluid grid-list-md text-xs-center>
        <v-layout row>
            <v-container>
                <v-btn color="info" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Add a Client
                </v-btn>
            </v-container>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12 md6 lg4 v-for="client in clients" :key="client.id">
                <v-card>
                    <v-card-title>{{ client.name }}</v-card-title>
                    <v-card-text>
                        {{ client.description }}
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>


        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="clientForm" @submit.prevent="formSubmit">
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
                        <v-btn type="submit" flat @click="createClient">Save</v-btn>
                        <v-btn flat color="primary" form="clientForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
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
        getClients() {
            axios.get('api/clients', { user_id: 1 })
            .then(response => {
                this.clients = response.data
            })
        },
        createClient() {
            let user_id = 1;
            let name = this.name;
            let description = this.description;

            axios.post('api/clients', { user_id, name, description })
            .then(response => {
                this.clients.push(response.data.data)
            })

            this.dialog = false;
        },
    },
    mounted() {
        this.getClients()
    }

}
</script>
