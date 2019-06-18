<template>
    <v-container fluid grid-list-md>
        <v-layout row>
            <v-container text-xs-center>
                <v-btn color="primary" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Add an Issue
                </v-btn>
            </v-container>
        </v-layout>

        <v-layout row>
            <v-container>
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
                    :items="issues"
                    :search="search"
                    :pagination.sync="pagination"
                    hide-actions
                    class="elevation-1"
                    no-data-text="You do not currently have any issues."
                >
                    <template v-slot:items="props">
                        <td>
                            <span v-if="parseInt(props.item.resolved) == 0">
                                <v-icon class="pointer" color="error" @click="resolveIssue(props.item.id)">remove_circle</v-icon>
                            </span>
                            <span v-else>
                                <v-icon class="pointer" color="success" @click="unresolveIssue(props.item.id)">check_circle</v-icon>
                            </span>
                        </td>
                        <td>{{ props.item.priority }}</td>
                        <td>{{ props.item.description | truncate(100) }}</td>
                        <td><span v-if="props.item.project">{{ props.item.project.name }}</span></td>
                        <td width="15%">
                            <span class="hidden">{{ props.item.created_at }}</span>
                            {{ props.item.created_at | fromNow() }}
                        </td>
                        <td width="25%">
                            <v-form method="POST" id="deleteForm" @submit.prevent="deleteIssue(props.item.id)">
                                <v-btn small :to="'/issue/' + props.item.id" color="primary" class="white--text">View</v-btn>
                                <v-btn small type="submit" color="red darken-1" class="white--text">Delete</v-btn>
                            </v-form>
                        </td>
                    </template>
                </v-data-table>
            </v-container>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="issueForm" @submit.prevent="addIssue" ref="form" lazy-validation>
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Issue</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="description" :rules="[v => !!v || 'Description is required']" required></v-textarea>
                            </v-flex>
                            <v-flex xs12>
                                 <v-autocomplete
                                    :items="projects"
                                    item-text="name"
                                    item-value="id"
                                    label="Select a project"
                                    prepend-icon="work"
                                    v-model="project_id"
                                    required
                                    :rules="[v => !!v || 'Project is required']"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12>
                                <v-radio-group v-model="priority" row required :rules="[v => !!v || 'Priority is required']">
                                    <template v-slot:label>
                                        <div>Priority:</div>
                                    </template>
                                    <v-radio label="1" value="1"></v-radio>
                                    <v-radio label="2" value="2"></v-radio>
                                    <v-radio label="3" value="3"></v-radio>
                                    <v-radio label="4" value="4"></v-radio>
                                    <v-radio label="5" value="5"></v-radio>
                                </v-radio-group>
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
    name: 'Issues',
    data() {
        return {
            dialog: false,
            description: '',
            projects: [],
            project_id: '',
            priority: '',
            issues: [],
            search: '',
            pagination: {
                sortBy: 'resolved',
                rowsPerPage: -1
            },
            headers: [
                { text: 'Status', value: 'resolved' },
                { text: 'Priority', value: 'priority' },
                { text: 'Description', value: 'description' },
                { text: 'Project', value: 'project.name' },
                { text: 'Created', value: 'created_at' },
                { text: 'Actions', value: 'actions', sortable: false },
            ],
        }
    },
    methods: {
        getIssues() {
            axios.get('/api/issues')
            .then(response => {
                this.issues = response.data
            })
        },
        getProjects() {
            axios.get('/api/projects')
            .then(response => {
                this.projects = response.data
            })
        },
        addIssue() {
            if (this.$refs.form.validate()) {
                let description = this.description
                let priority = this.priority
                let project_id = this.project_id

                axios.post('/api/issues', { description, priority, project_id })
                .then(response => {
                    this.getIssues()

                    Event.$emit('success', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })

                this.reset()
            }
        },
        resolveIssue(issue_id) {
            axios.post('/api/issues/' + issue_id + '/resolve')
            .then(response => {
                this.getIssues()

                Event.$emit('success', response.data.message)
            })
            .catch(function (error) {
                Event.$emit('error', response.data.message)
            })
        },
        unresolveIssue(issue_id) {
            axios.post('/api/issues/' + issue_id + '/unresolve')
            .then(response => {
                this.getIssues()

                Event.$emit('warning', response.data.message)
            })
            .catch(function (error) {
                Event.$emit('error', response.data.message)
            })
        },
        deleteIssue(issue_id) {
            axios.delete('/api/issues/' + issue_id)
            .then(response => {
                this.getIssues()

                Event.$emit('success', response.data.message)
            })
            .catch(function (error) {
                Event.$emit('error', response.data.message)
            })
        },
        reset() {
            this.dialog = false

            this.project_id = ''
            this.priority = ''
            this.description = ''
        }
    },
    mounted() {
        this.getIssues()
        this.getProjects()
    }

}
</script>
