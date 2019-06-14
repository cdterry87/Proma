<template>
    <div>
        <v-container fluid grid-list-md>
            <v-layout row>
                <v-flex xs12>
                    <v-card>
                        <v-alert :value="true" v-if="issue.resolved" type="success" @click="unresolveIssue(issue.id)" class="clickable">
                            Issue resolved {{ issue.resolved_date }}.
                        </v-alert>
                        <v-alert :value="true" v-else type="warning" @click="resolveIssue(issue.id)" class="clickable">
                            <span>Issue is unresolved.</span>
                        </v-alert>
                        <v-card-text>
                             <v-layout align-baseline>
                                <v-flex xs6>
                                    <span class="title" v-if="issue.project">
                                        <v-icon>star</v-icon>
                                        Priority: {{ issue.priority }}
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
                                {{ issue.description }}
                            </div>
                            <div class="mt-4" v-if="issue.project">
                                <span class="title">
                                    <v-icon>work</v-icon> Project
                                </span>
                                <div>
                                    {{ issue.project.name }}
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>

        <IssueNotes :issueInfo="issue" :issueNotes="notes" />

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="editIssueForm" @submit.prevent="updateIssue">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Edit Issue</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="work" label="Project Name" v-model="issue.name" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-autocomplete
                                    :items="projects"
                                    item-text="name"
                                    item-value="id"
                                    label="Select a project"
                                    prepend-icon="work"
                                    v-model="issue.project_id"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="issue.description"></v-textarea>
                            </v-flex>
                            <v-flex xs12>
                                <v-radio-group v-model="issue.priority" row>
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
                        <v-btn flat color="red darken-2" form="editProjectForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>

        <v-snackbar v-model="snackbar.enabled" :color="snackbar.color" :bottom="true" :right="true" :timeout="snackbar.timeout">
            {{ snackbar.message }}
            <v-btn color="white" flat @click="snackbar.enabled = false"><v-icon>close</v-icon></v-btn>
        </v-snackbar>
    </div>
</template>

<script>
    import EventBus from './../eventbus'
    import IssueNotes from './IssueNotes'

    export default {
        name: 'Issue',
        props: ['id'],
        components: {
            IssueNotes
        },
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
                issue: '',
                notes: '',
                projects: []
            }
        },
        methods: {
            getIssue() {
                axios.get('/api/issues/' + this.id)
                .then(response => {
                    this.issue = response.data

                    this.getIssueNotes(this.id);
                })
            },
            getProjects() {
                axios.get('/api/projects')
                .then(response => {
                    this.projects = response.data
                })
            },
            getIssueNotes(issue_id) {
                axios.get('/api/notes/' + issue_id)
                .then(response => {
                    this.notes = response.data
                })
            },
            updateIssue() {
                let description = this.issue.description;
                let priority = this.issue.priority;
                let project_id = this.issue.project_id;

                axios.put('/api/issues/' + this.issue.id, { priority, description, project_id })
                .then(response => {
                    this.snackbar.color = 'success'
                    this.snackbar.message = "Issue updated successfully!"
                    this.snackbar.enabled = true
                })
                .catch(function (error) {
                    this.snackbar.color = 'error'
                    this.snackbar.message = "Error updating issue!"
                    this.snackbar.enabled = true
                })

                this.reset()
            },
            resolveIssue(issue_id) {
                axios.post('/api/issues/' + issue_id + '/resolve')
                .then(response => {
                    this.getIssue()

                    this.snackbar.color = 'success'
                    this.snackbar.message = "Issue is now resolved!"
                    this.snackbar.enabled = true
                })
                .catch(function (error) {
                    this.snackbar.color = 'danger'
                    this.snackbar.message = "Issue could not be resolved!"
                    this.snackbar.enabled = true
                })
            },
            unresolveIssue(issue_id) {
                axios.post('/api/issues/' + issue_id + '/unresolve')
                .then(response => {
                    this.getIssue()

                    this.snackbar.color = 'warning'
                    this.snackbar.message = "Issue is now unresolved!"
                    this.snackbar.enabled = true
                })
                .catch(function (error) {
                    this.snackbar.color = 'danger'
                    this.snackbar.message = "Issue could not be marked as unresolved!"
                    this.snackbar.enabled = true
                })
            },
            reset() {
                this.dialog = false
                this.priority = ''
                this.project_id = ''
                this.description = ''
            }
        },
        created() {
            EventBus.$on('loadNotes', issue_id => {
                this.getIssueNotes(issue_id)
            })
        },
        mounted() {
            this.getIssue()
            this.getProjects()
        }
    }
</script>
