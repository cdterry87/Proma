<template>
    <v-container fluid grid-list-md>
        <v-layout align-baseline>
            <v-flex xs12>
                <v-card>
                    <v-card-text>
                        <v-layout align-baseline>
                            <v-flex xs6>
                                <span class="headline">
                                    <v-icon>notes</v-icon> Notes
                                </span>
                            </v-flex>
                            <v-flex xs6 text-xs-right>
                                <v-btn color="primary" @click="addNote" small>
                                    <v-icon left dark>add</v-icon>
                                    Add Note
                                </v-btn>
                            </v-flex>
                        </v-layout>

                        <v-content v-if="issueNotes">
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
                                :items="issueNotes"
                                :search="search"
                                :pagination.sync="pagination"
                                hide-actions
                                no-data-text="This issue does not currently have any notes."
                            >
                                <template v-slot:items="props">
                                    <td>{{ props.item.description | truncate(150) }}</td>
                                    <td width="15%">{{ props.item.created_at | fromNow() }}</td>
                                    <td width="25%">
                                        <v-form method="POST" id="deleteForm" @submit.prevent="deleteNote(props.item.issue_id, props.item.id)">
                                            <v-btn small color="primary" class="white--text" @click="editNote(props.item)">Edit</v-btn>
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
            <v-form method="POST" id="noteForm" @submit.prevent="saveNote">
                <input type="hidden" name="note_id" v-model="note_id">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Save Note</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="description" required></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat color="blue darken-2">Save</v-btn>
                        <v-btn flat color="red darken-2" form="noteForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </v-container>
</template>

<script>
    import Event from './../events';

    export default {
        name: 'IssueNotes',
        props: ['issueInfo', 'issueNotes'],
        data() {
            return {
                dialog: false,
                description: '',
                note_id: '',
                search: '',
                pagination: {
                    sortBy: 'created_at',
                    rowsPerPage: -1
                },
                headers: [
                    { text: 'Description', value: 'description' },
                    { text: 'Created', value: 'created_at' },
                    { text: 'Actions', value: 'actions', sortable: false },
                ],
            }
        },
        methods: {
            addNote() {
                this.dialog = true

                this.description = ''
                this.note_id = ''
            },
            editNote(note) {
                this.dialog = true

                this.description = note.description
                this.note_id = note.id
            },
            saveNote() {
                let description = this.description
                let issue_id = this.issueInfo.id

                let note_id = this.note_id

                let method = (!_.isNumber(note_id) ? 'post' : 'put')

                axios({
                    method: method,
                    url: '/api/notes/' + note_id,
                    data: { description, issue_id }
                })
                .then(response => {
                    Event.$emit('loadNotes', issue_id)

                    Event.$emit('success', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })

                this.reset()
            },
            deleteNote(issue_id, note_id) {
                axios.delete('/api/notes/' + note_id)
                .then(response => {
                    Event.$emit('loadNotes', issue_id)

                    Event.$emit('success', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })
            },
            reset() {
                this.dialog = false
                this.description = ''
            }
        },
    }
</script>
