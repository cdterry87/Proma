<template>
    <v-container fluid grid-list-md>
        <h2>Edit Client</h2>
        <v-form method="POST" id="editClientForm" @submit.prevent="updateClient">
            <v-layout row>
                <v-flex xs12>
                    <v-text-field prepend-icon="person" label="Client Name" v-model="client.name"></v-text-field>
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
</template>

<script>
    export default {
        name: 'EditClient',
        props: ['id'],
        data() {
            return {
                client: ''
            }
        },
        methods: {
            getUserData() {
                this.userData = JSON.parse(localStorage.getItem('userData'))
            },
            getClient() {
                axios.get('/api/clients/' + this.id)
                .then(response => {
                    this.client = response.data
                })
            },
            updateClient() {
                let name = this.client.name;
                let description = this.client.description;

                axios.put('/api/clients/' + this.id, { name, description })
                .then(response => {
                    this.client = response.data.data
                })
            },
        },
        created() {
            this.getUserData()
        },
        mounted() {
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt

            this.getClient()
        }
    }
</script>

<style scoped>

</style>
