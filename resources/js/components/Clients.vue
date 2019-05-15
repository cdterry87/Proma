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
            <v-container v-if="clients.length == 0">
                You do not currently have any clients.
            </v-container>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12 md6 lg4 v-for="client in clients" :key="client.id">
                <router-link :to="'/client/' + client.id">
                    <v-card class="data-card">
                        <v-card-text>
                            <div class="headline">
                                {{ client.name | truncate(25) }}
                            </div>
                            {{ client.description | truncate(150) }}
                        </v-card-text>
                    </v-card>
                </router-link>
            </v-flex>
        </v-layout>


        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="clientForm" @submit.prevent="createClient">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Create Client</v-card-title>
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
                        <v-btn type="submit" flat>Save</v-btn>
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
            axios.get('/api/clients')
            .then(response => {
                this.clients = response.data
            })
        },
        createClient() {
            let name = this.name
            let description = this.description

            axios.post('/api/clients', { name, description })
            .then(response => {
                this.clients.push(response.data.data)
            })

            this.reset()
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

<style scoped>

</style>
