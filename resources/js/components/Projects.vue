<template>
    <v-container fluid grid-list-md>
        <v-layout row>
            <v-container text-xs-center>
                <v-btn color="info" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Add a Project
                </v-btn>
            </v-container>
        </v-layout>
        <v-layout row text-xs-center>
            <v-container v-if="projects.length == 0">
                You do not currently have any projects.
            </v-container>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12 md6 lg4 v-for="project in projects" :key="project.id">
                <v-card class="data-card">
                    <v-alert :value="true" v-if="project.complete" type="success" @click="incompleteProject(project.id)">
                        Project completed {{ project.completed_date }}.
                    </v-alert>
                    <v-alert :value="true" v-else-if="!project.complete && project.due_date != '' && project.due_date != null && new Date(project.due_date) < Date.now()" type="error" @click="completeProject(project.id)">
                        Project was due {{ project.due_date }}.
                    </v-alert>
                    <v-alert :value="true" v-else-if="!project.complete" type="warning" @click="completeProject(project.id)">
                        <span v-if="project.due_date">Project is due {{ project.due_date }}.</span>
                        <span v-else>Project is incomplete.</span>
                    </v-alert>
                    <router-link :to="'/project/' + project.id">
                        <v-card-text>
                            <div class="headline">
                                {{ project.name | truncate(25) }}
                            </div>
                            {{ project.description | truncate(150) }}
                        </v-card-text>
                    </router-link>
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
                                <v-autocomplete
                                    :items="teams"
                                    item-text="name"
                                    item-value="id"
                                    label="Select a team"
                                    prepend-icon="people"
                                    v-model="team_id"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12>
                                 <v-autocomplete
                                    :items="clients"
                                    item-text="name"
                                    item-value="id"
                                    label="Select a client"
                                    prepend-icon="person"
                                    v-model="client_id"
                                ></v-autocomplete>
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

        <v-snackbar v-model="snackbar.enabled" :color="snackbar.color" :bottom="true" :right="true" :timeout="snackbar.timeout">
            {{ snackbar.message }}
            <v-btn color="white" flat @click="snackbar.enabled = false"><v-icon>close</v-icon></v-btn>
        </v-snackbar>
    </v-container>
</template>

<script>
export default {
    name: 'Projects',
    data() {
        return {
            dialog: false,
            date_dialog: false,
            snackbar: {
                enabled: false,
                message: '',
                timeout: 5000,
                y: 'bottom',
                x: 'right',
                color: ''
            },
            name: '',
            description: '',
            due_date: '',
            team_id: '',
            client_id: '',
            projects: [],
            teams: [],
            clients: [],
        }
    },
    methods: {
        dueDate(due_date) {
            this.date_dialog = false
            this.$refs.datePicker.save(due_date)
        },
        getProjects() {
            axios.get('/api/projects')
            .then(response => {
                this.projects = response.data
            })
        },
        getClients() {
            axios.get('/api/clients')
            .then(response => {
                this.clients = response.data
            })
        },
        getTeams() {
            axios.get('/api/teams')
            .then(response => {
                this.teams = response.data
            })
        },
        addProject() {
            let name = this.name
            let description = this.description
            let due_date = this.due_date
            let client_id = this.client_id
            let team_id = this.team_id

            axios.post('/api/projects', { name, description, due_date, client_id, team_id })
            .then(response => {
                this.projects.push(response.data.data)

                this.snackbar.color = 'success'
                this.snackbar.message = "Project successfully created!"
                this.snackbar.enabled = true
            })
            .catch(function (error) {
                this.snackbar.color = 'error'
                this.snackbar.message = "Error creating project!"
                this.snackbar.enabled = true
            })

            this.reset()
        },
        completeProject(project_id) {
            axios.post('/api/projects/' + project_id + '/complete')
            .then(response => {
                this.getProjects()

                this.snackbar.color = 'success'
                this.snackbar.message = "Project is now complete!"
                this.snackbar.enabled = true
            })
            .catch(function (error) {
                this.snackbar.color = 'danger'
                this.snackbar.message = "Project could not be completed!"
                this.snackbar.enabled = true
            })
        },
        incompleteProject(project_id) {
            axios.post('/api/projects/' + project_id + '/incomplete')
            .then(response => {
                this.getProjects()

                this.snackbar.color = 'warning'
                this.snackbar.message = "Project is now incomplete!"
                this.snackbar.enabled = true
            })
            .catch(function (error) {
                this.snackbar.color = 'danger'
                this.snackbar.message = "Project could not be changed to incomplete!"
                this.snackbar.enabled = true
            })
        },
        reset() {
            this.dialog = false
            this.name = ''
            this.client_id = ''
            this.team_id = ''
            this.description = ''
        }
    },
    mounted() {
        this.getProjects()
        this.getClients()
        this.getTeams()
    }

}
</script>
