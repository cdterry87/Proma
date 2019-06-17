<template>
    <v-container fluid grid-list-md>
        <v-layout align-baseline>
            <v-flex xs12>
                <v-card>
                    <v-card-text>
                        <v-layout align-baseline>
                            <v-flex xs6>
                                <span class="headline">
                                    <v-icon>work</v-icon> Projects
                                </span>
                            </v-flex>
                            <v-flex xs6 text-xs-right>
                                <v-btn color="primary" @click="dialog = true" small>
                                    <v-icon left dark>add</v-icon>
                                    Add Project
                                </v-btn>
                            </v-flex>
                        </v-layout>
                        <v-content v-if="clientProjects">
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
                                :items="clientProjects"
                                :search="search"
                                :pagination.sync="pagination"
                                hide-actions
                                class="elevation-1"
                                no-data-text="This client does not currently have any projects."
                                disable-initial-sort
                            >
                                <template v-slot:items="props">
                                    <td>
                                        <span v-if="parseInt(props.item.completed)">
                                            <v-icon class="pointer" color="success" @click="incompleteProject(props.item.id)">check_circle</v-icon>
                                        </span>
                                        <span v-else-if="!parseInt(props.item.completed) && props.item.due_date != '' && props.item.due_date != null && new Date(props.item.due_date) < Date.now()">
                                            <v-icon class="pointer" color="error" @click="completeProject(props.item.id)">warning</v-icon>
                                        </span>
                                        <span v-else-if="!parseInt(props.item.completed)">
                                            <v-icon class="pointer" color="warning" @click="completeProject(props.item.id)">remove_circle</v-icon>
                                        </span>
                                    </td>
                                    <td>{{ props.item.name }}</td>
                                    <td width="15%">
                                        <span class="hidden">{{ props.item.due_date }}</span>
                                        {{ props.item.due_date | fromNow() }}
                                    </td>
                                    <td width="25%">
                                        <v-form method="POST" id="deleteForm" @submit.prevent="deleteProject(props.item.id)">
                                            <v-btn small :to="'/project/' + props.item.id" color="primary" class="white--text">View</v-btn>
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
            <v-form method="POST" id="projectForm" @submit.prevent="addProject">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Project</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="work" label="Project Name" v-model="name" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="description"></v-textarea>

                                <v-dialog
                                ref="datePicker"
                                v-model="date_dialog"
                                :return-value.sync="due_date"
                                persistent
                                lazy
                                full-width
                                width="290px"
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                        v-model="due_date"
                                        label="Due Date"
                                        prepend-icon="event"
                                        readonly
                                        v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker v-model="due_date" scrollable @change="dueDate">
                                        <v-spacer></v-spacer>
                                        <v-btn flat color="primary" @click="date_dialog = false">Cancel</v-btn>
                                        <v-btn flat color="primary" @click="$refs.dialog.save(due_date)">OK</v-btn>
                                        <v-spacer></v-spacer>
                                    </v-date-picker>
                                </v-dialog>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat color="blue darken-2">Save</v-btn>
                        <v-btn flat color="red darken-2" form="projectForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </v-container>
</template>

<script>
    import Event from './../events'

    export default {
        name: 'ClientProjects',
        props: ['clientInfo', 'clientProjects'],
        data() {
            return {
                dialog: false,
                date_dialog: false,
                name: '',
                description: '',
                due_date: '',
                search: '',
                pagination: {
                    sortBy: 'completed',
                    rowsPerPage: -1
                },
                headers: [
                    { text: 'Status', value: 'completed' },
                    { text: 'Name', value: 'name' },
                    { text: 'Due', value: 'due_date' },
                    { text: 'Actions', value: 'actions', sortable: false },
                ],
            }
        },
        methods: {
            dueDate(due_date) {
                this.date_dialog = false
                this.$refs.datePicker.save(due_date)
            },
            addProject() {
                let name = this.name
                let description = this.description
                let due_date = this.due_date
                let client_id = this.clientInfo.id

                axios.post('/api/projects', { name, description, due_date, client_id })
                .then(response => {
                    Event.$emit('loadProjects', this.clientInfo.id)

                    Event.$emit('success', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })

                this.reset()
            },
            completeProject(project_id) {
                axios.post('/api/projects/' + project_id + '/complete')
                .then(response => {
                    Event.$emit('loadProjects', this.clientInfo.id)

                    Event.$emit('success', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })
            },
            incompleteProject(project_id) {
                axios.post('/api/projects/' + project_id + '/incomplete')
                .then(response => {
                    Event.$emit('loadProjects', this.clientInfo.id)

                    Event.$emit('warning', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })
            },
            deleteProject(project_id) {
                axios.delete('/api/projects/' + project_id)
                .then(response => {
                    Event.$emit('loadProjects', this.clientInfo.id)

                    Event.$emit('error', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('success', response.data.message)
                })
            },
            reset() {
                this.dialog = false
                this.name = ''
                this.description = ''
            }
        },
    }
</script>
