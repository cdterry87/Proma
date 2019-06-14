<template>
    <v-container fluid grid-list-md>
        <v-layout row>
            <v-container text-xs-center>
                <v-btn color="info" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Add an Issue
                </v-btn>
            </v-container>
        </v-layout>
        <v-layout row text-xs-center>
            <v-container v-if="issues.length == 0" class="headline">
                You do not currently have any issues..
            </v-container>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12 md6 lg4 v-for="issue in issues" :key="issue.id">
                <v-card class="data-card medium">
                    <router-link :to="'/issues/' + issue.id">
                        <span class="title">#{{ issue.id }}</span>
                        <v-card-text>
                            {{ issue.description | truncate(150) }}
                        </v-card-text>
                    </router-link>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn flat color="red darken-2" @click="removeIssue(issue.id)">Remove</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="issueForm" @submit.prevent="addIssue">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Issue</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                 <v-autocomplete
                                    :items="projects"
                                    item-text="name"
                                    item-value="id"
                                    label="Select a project"
                                    prepend-icon="work"
                                    v-model="project_id"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="description"></v-textarea>
                            </v-flex>
                            <v-flex xs12>
                                <v-radio-group v-model="priority" row>
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

        <v-snackbar v-model="snackbar.enabled" :color="snackbar.color" :bottom="true" :right="true" :timeout="snackbar.timeout">
            {{ snackbar.message }}
            <v-btn color="white" flat @click="snackbar.enabled = false"><v-icon>close</v-icon></v-btn>
        </v-snackbar>
    </v-container>
</template>

<script>
export default {
    name: 'Issues',
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
            description: '',
            projects: [],
            project_id: '',
            priority: '',
            issues: [],
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
            let description = this.description
            let priority = this.priority
            let project_id = this.project_id

            axios.post('/api/issues', { description, priority, project_id })
            .then(response => {
                this.issues.push(response.data.data)

                this.snackbar.color = 'success'
                this.snackbar.message = "Issue successfully created!"
                this.snackbar.enabled = true
            })
            .catch(function (error) {
                this.snackbar.color = 'error'
                this.snackbar.message = "Error creating issue!"
                this.snackbar.enabled = true
            })

            this.reset()
        },
        removeIssue(issue_id) {
            axios.delete('/api/issues/' + issue_id)
            .then(response => {
                this.getIssues()

                this.snackbar.color = 'success'
                this.snackbar.message = "Project successfully removed!"
                this.snackbar.enabled = true
            })
        },
        reset() {
            this.dialog = false
            this.project_id = ''
            this.description = ''
        }
    },
    mounted() {
        this.getIssues()
        this.getProjects()
    }

}
</script>
