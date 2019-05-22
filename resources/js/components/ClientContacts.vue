<template>
    <v-container fluid grid-list-md>
        <v-layout align-baseline>
            <v-flex xs6>
                <span class="headline">
                    <v-icon>phone</v-icon> Contacts
                </span>
            </v-flex>
            <v-flex xs6 text-xs-right>
                <v-btn color="info" @click="dialog = true" small>
                    <v-icon left dark>add</v-icon>
                    Add Contact
                </v-btn>
            </v-flex>
        </v-layout>
        <v-layout row v-if="clientContacts.length == 0">
            There are currently no contacts for this client.
        </v-layout>
        <v-layout row wrap v-else>
            <v-flex xs12 md6 lg4 v-for="contact in clientContacts" :key="contact.id">
                <v-card class="data-card">
                    <v-card-text>
                        <div>
                            <div class="headline">{{ contact.name | truncate(20) }}</div>
                            <span class="grey--text">{{ contact.title | truncate(30)}}</span>
                        </div>
                        <v-layout>
                            <v-flex>
                                <div><i class="material-icons">mail</i> {{ contact.email | truncate(30) }}</div>
                                <div><i class="material-icons">phone</i> {{ contact.phone }}</div>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="contactForm" @submit.prevent="addContact">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Contact</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="person" label="Contact Name" v-model="name" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field prepend-icon="work" label="Contact Title" v-model="title" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field prepend-icon="mail" label="Contact Email" v-model="email" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-text-field prepend-icon="phone" label="Contact Phone" v-model="phone" maxlength="30"></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat color="blue darken-2">Save</v-btn>
                        <v-btn flat color="red darken-2" form="contactForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </v-container>
</template>

<script>
    import EventBus from './../eventbus'

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
            }
        },
        methods: {
            addContact() {
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

                    EventBus.$emit('addContact', contacts)
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
    }
</script>
