<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container>
                <v-layout align-baseline>
                    <v-flex xs6>
                        <span class="headline">
                            <v-icon>list</v-icon> Contacts
                        </span>
                    </v-flex>
                    <v-flex xs6 text-xs-right>
                        <v-btn color="info" @click="dialog = true" small>
                            <v-icon left dark>add</v-icon>
                            Add Contact
                        </v-btn>
                    </v-flex>
                </v-layout>
            </v-container>
            <v-container v-if="clientContacts.length == 0">
                <v-layout row>
                    There are currently no contacts for this client.
                </v-layout>
            </v-container>
            <v-container v-else fluid grid-list-md>
                <v-layout row wrap>
                    <v-flex xs12 md6 lg4 v-for="contact in clientContacts" :key="contact.id">
                        <v-card>
                            <v-card-text>
                                <div>
                                    <div class="headline">{{ contact.name | truncate(25) }}</div>
                                    <span class="grey--text">{{ contact.title }}</span>
                                </div>
                                <v-layout>
                                    <v-flex>
                                        <div><i class="material-icons">mail</i> {{ contact.email }}</div>
                                        <div><i class="material-icons">phone</i> {{ contact.phone }}</div>
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                            <v-card-actions>
                                <v-flex xs6 offset-xs3>
                                    <v-btn color="info" block small><i class="material-icons">edit</i> Edit</v-btn>
                                </v-flex>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="contactForm" @submit.prevent="createContact">
                <v-card>
                    <v-card-title class="grey lighten-4 py-4 title">Create Contact</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="person" label="Contact Name" v-model="name"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field prepend-icon="work" label="Contact Title" v-model="title"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field prepend-icon="mail" label="Contact Email" v-model="email"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field prepend-icon="phone" label="Contact Phone" v-model="phone"></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat>Save</v-btn>
                        <v-btn flat color="primary" form="taskForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </v-container>
</template>

<script>
    import eventBus from './../events';

    export default {
        name: 'ClientContacts',
        props: ['clientInfo', 'clientContacts'],
        data() {
            return {
                dialog: false,
                name: '',
                title: '',
                email: '',
                phone: '',
                userData: null,
            }
        },
        methods: {
            getUserData() {
                this.userData = JSON.parse(localStorage.getItem('userData'))
            },
            createContact() {
                let name = this.name
                let title = this.title
                let email = this.email
                let phone = this.phone
                let client_id = this.clientInfo.id

                axios.post('/api/contacts', { name, title, email, phone, client_id })
                .then(response => {
                    this.contacts = this.clientContacts
                    this.contacts.push(response.data.data)

                    let contacts = this.contacts

                    eventBus.$emit('createContact', contacts)
                })

                this.reset()
            },
            reset() {
                this.dialog = false
                this.name = ''
                this.title = ''
                this.email = ''
                this.phone = ''
            }
        },
        created() {
            this.getUserData()
        },
        mounted() {
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt
        },
    }
</script>
