<template>
    <div>
        <v-container fluid grid-list-md>
            <v-layout row>
                <v-flex xs12>
                    <v-card>
                        <v-card-text>
                             <v-layout align-baseline>
                                <v-flex xs6>
                                    <span class="title">
                                        <v-icon>person</v-icon>
                                        {{ client.name }}
                                    </span>
                                </v-flex>
                                <v-flex xs6 text-xs-right>
                                    <v-btn color="primary" @click="dialog = true" small>
                                        <v-icon left dark>edit</v-icon>
                                        Edit
                                    </v-btn>
                                </v-flex>
                            </v-layout>
                            <div>
                                {{ client.description }}
                            </div>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>

        <ClientContacts :client="client" />
        <ClientProjects :client="client" />
        <FileUpload :uploadInfo="client" uploadType="client" />

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="editClientForm" @submit.prevent="updateClient" ref="form" lazy-validation>
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Edit Client</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="business" label="Client Name" v-model="client.name" maxlength="100" :rules="[v => !!v || 'Name is required']" required></v-text-field>
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
    import Event from './../events'
    import ClientContacts from './ClientContacts'
    import ClientProjects from './ClientProjects'
    import FileUpload from './FileUpload'

    export default {
        name: 'Client',
        props: ['id'],
        components: {
            ClientContacts,
            ClientProjects,
            FileUpload
        },
        data() {
            return {
                dialog: false,
                client: '',
            }
        },
        methods: {
            getClient() {
                axios.get('/api/clients/' + this.id)
                .then(response => {
                    this.client = response.data
                })
            },
            updateClient() {
                if (this.$refs.form.validate()) {
                    let name = this.client.name;
                    let description = this.client.description;

                    axios.put('/api/clients/' + this.client.id, { name, description })
                    .then(response => {
                        Event.$emit('success', response.data.message)
                    })
                    .catch(function (error) {
                        Event.$emit('error', response.data.message)
                    })

                    this.reset()
                }
            },
            reset() {
                this.dialog = false
                this.name = ''
                this.description = ''
            }
        },
        watch: {
            '$route' (to, from) {
                this.getClient()
            }
        },
        created() {
            Event.$on('dataRefresh', client_id => {
                this.getClient(client_id)
            })
        },
        mounted() {
            this.getClient()
        }
    }
</script>
