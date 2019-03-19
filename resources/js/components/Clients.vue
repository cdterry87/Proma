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
        <v-layout row wrap>
            <v-flex xs12 md6 lg4 v-for="client in clients" :key="client.id">
                <v-card>
                    <v-card-title primary-title>
                        <div class="headline">
                            {{ client.name | truncate(25) }}
                        </div>
                    </v-card-title>
                    <v-card-text>
                        {{ client.description | truncate(80) }}
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>


        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="clientForm" @submit.prevent="createClient">
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
            userData: null,
            clients: []
        }
    },
    methods: {
        getUserData() {
            this.userData = JSON.parse(localStorage.getItem('userData'))
        },
        getClients() {
            let user_id = this.userData.id

            axios.get('api/clients', { user_id })
            .then(response => {
                this.clients = response.data
            })
        },
        createClient() {
            let user_id = this.userData.id
            let name = this.name
            let description = this.description

            axios.post('api/clients', { user_id, name, description })
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
    created() {
        this.getUserData()
    },
    mounted() {
        axios.defaults.headers.common['Content-Type'] = 'application/json'
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt

        this.getClients()
    }

}
</script>

<style scoped>
.container {
    padding-top: 6px !important;
    padding-bottom: 6px !important;
}
</style>
