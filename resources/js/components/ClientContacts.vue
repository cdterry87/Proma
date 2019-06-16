<template>
    <v-container fluid grid-list-md>
        <v-layout align-baseline>
            <v-flex xs12>
                <v-card>
                    <v-card-text>
                        <v-layout align-baseline>
                            <v-flex xs6>
                                <span class="headline">
                                    <v-icon>phone</v-icon> Contacts
                                </span>
                            </v-flex>
                            <v-flex xs6 text-xs-right>
                                <v-btn color="primary" @click="dialog = true" small>
                                    <v-icon left dark>add</v-icon>
                                    Add Contact
                                </v-btn>
                            </v-flex>
                        </v-layout>
                        <v-content v-if="clientContacts">
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
                                :items="clientContacts"
                                :search="search"
                                :pagination.sync="pagination"
                                hide-actions
                                class="elevation-1"
                                no-data-text="This client does not currently have any contacts."
                            >
                                <template v-slot:items="props">
                                    <td>{{ props.item.name }}</td>
                                    <td>{{ props.item.title }}</td>
                                    <td>{{ props.item.email }}</td>
                                    <td>{{ props.item.phone }}</td>
                                    <td width="25%">
                                        <v-form method="POST" id="deleteForm" @submit.prevent="deleteContact(props.item.client_id, props.item.id)">
                                            <v-btn small @click="editContact(props.item)" color="primary" class="white--text">Edit</v-btn>
                                            <v-btn small type="submit" color="red darken-1" class="white--text">Delete</v-btn>
                                        </v-form>
                                    </td>
                                </template>
                            </v-data-table>
                        </v-content>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="contactForm" @submit.prevent="saveContact">
                <input type="hidden" name="contact_id" v-model="contact_id">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Save Contact</v-card-title>
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

        <v-snackbar v-model="snackbar.enabled" :color="snackbar.color" :bottom="true" :right="true" :timeout="snackbar.timeout">
            {{ snackbar.message }}
            <v-btn color="white" flat @click="snackbar.enabled = false"><v-icon>close</v-icon></v-btn>
        </v-snackbar>
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
                snackbar: {
                    enabled: false,
                    message: '',
                    timeout: 5000,
                    y: 'bottom',
                    x: 'right',
                    color: ''
                },
                name: '',
                title: '',
                email: '',
                phone: '',
                contact_id: '',
                search: '',
                pagination: {
                    sortBy: 'name',
                    rowsPerPage: -1
                },
                headers: [
                    { text: 'Name', value: 'name' },
                    { text: 'Title', value: 'title' },
                    { text: 'Email', value: 'email' },
                    { text: 'Phone', value: 'phone' },
                    { text: 'Actions', value: 'actions', sortable: false },
                ],
            }
        },
        methods: {
            newContact() {
                this.reset()

                this.dialog = true
            },
            editContact(contact) {
                this.dialog = true

                this.name = contact.name
                this.title = contact.title
                this.email = contact.email
                this.phone = contact.phone
                this.contact_id = contact.id
            },
            saveContact() {
                let name = this.name
                let title = this.title
                let email = this.email
                let phone = this.phone
                let client_id = this.clientInfo.id

                let contact_id = this.contact_id

                let method = (!_.isNumber(contact_id) ? 'post' : 'put')

                axios({
                    method: method,
                    url: '/api/contacts/' + contact_id,
                    data: { name, title, email, phone, client_id }
                })
                .then(response => {
                    EventBus.$emit('loadContacts', client_id)

                    this.snackbar.color = 'success'
                    this.snackbar.message = "Contact successfully added!"
                    this.snackbar.enabled = true
                })
                .catch(function (error) {
                    this.snackbar.color = 'error'
                    this.snackbar.message = "Error adding contact!"
                    this.snackbar.enabled = true
                })

                this.reset()
            },
            deleteContact(client_id, contact_id) {
                axios.delete('/api/contacts/' + contact_id)
                .then(response => {
                    EventBus.$emit('loadContacts', client_id)

                    this.snackbar.color = 'success'
                    this.snackbar.message = "Contact successfully removed!"
                    this.snackbar.enabled = true
                })
            },
            reset() {
                this.dialog = false

                this.name = ''
                this.title = ''
                this.email = ''
                this.phone = ''
                this.contact_id = ''
            }
        },
    }
</script>
