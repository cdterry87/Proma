<template>
    <div>
        <v-container fluid grid-list-md>
            <v-layout align-baseline>
                <v-flex xs12 class="clickable">
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
                </v-flex>
            </v-layout>

            <v-layout row>
                <v-flex xs12>
                    <v-card>
                        <v-card-text>
                             <v-layout align-baseline>
                                <v-flex xs6>
                                    <span class="headline">
                                        {{ project.name }}
                                    </span>
                                </v-flex>
                                <v-flex xs6 text-xs-right>
                                    <v-btn color="info" @click="dialog = true" small>
                                        <v-icon left dark>edit</v-icon>
                                        Edit
                                    </v-btn>
                                </v-flex>
                            </v-layout>
                            <div>
                                {{ project.description }}
                            </div>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>

        <v-container fluid grid-list-md>
            <v-layout align-baseline>
                <v-flex xs6 v-if="project.team">
                    <v-card>
                        <v-card-text>
                            <span class="headline">
                                <v-icon>people</v-icon> Team
                            </span>
                            <div>
                                {{ project.team.name }}
                            </div>
                        </v-card-text>
                    </v-card>
                </v-flex>
                <v-flex xs6 v-if="project.client">
                    <v-card>
                        <v-card-text>
                            <span class="headline">
                                <v-icon>person</v-icon> Client
                            </span>
                            <div>
                                {{ project.client.name }}
                            </div>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>

        <ProjectTasks :projectInfo="project" :projectTasks="tasks" />

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="editProjectForm" @submit.prevent="updateProject">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Edit Project</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="work" label="Project Name" v-model="project.name" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-autocomplete
                                    :items="teams"
                                    item-text="name"
                                    item-value="id"
                                    label="Select a team"
                                    prepend-icon="people"
                                    v-model="project.team_id"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12>
                                 <v-autocomplete
                                    :items="clients"
                                    item-text="name"
                                    item-value="id"
                                    label="Select a client"
                                    prepend-icon="person"
                                    v-model="project.client_id"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="project.description"></v-textarea>

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
                                        v-model="project.due_date"
                                        label="Due Date"
                                        prepend-icon="event"
                                        readonly
                                        v-on="on"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker v-model="project.due_date" scrollable @change="dueDate">
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
    import ProjectTasks from './ProjectTasks'

    export default {
        name: 'Project',
        props: ['id'],
        components: {
            ProjectTasks
        },
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
                project: '',
                tasks: '',
                teams: [],
                clients: []
            }
        },
        methods: {
            dueDate(due_date) {
                this.date_dialog = false
                this.$refs.datePicker.save(due_date)
            },
            getProject() {
                axios.get('/api/projects/' + this.id)
                .then(response => {
                    this.project = response.data

                    this.getProjectTasks(this.id);
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
            getProjectTasks(project_id) {
                axios.get('/api/tasks/' + project_id)
                .then(response => {
                    this.tasks = response.data
                })
            },
            updateProject() {
                let name = this.project.name;
                let description = this.project.description;
                let client_id = this.project.client_id;
                let team_id = this.project.team_id;

                axios.put('/api/projects/' + this.project.id, { name, description, client_id, team_id })
                .then(response => {
                    // this.project = response.data.data

                    this.snackbar.color = 'success'
                    this.snackbar.message = "Project updated successfully!"
                    this.snackbar.enabled = true
                })
                .catch(function (error) {
                    this.snackbar.color = 'error'
                    this.snackbar.message = "Error updating project!"
                    this.snackbar.enabled = true
                })

                this.reset()
            },
            completeProject(project_id) {
                axios.post('/api/projects/' + project_id + '/complete')
                .then(response => {
                    this.getProject()

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
                    this.getProject()

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
        created() {
            EventBus.$on('addTask', tasks => {
                this.tasks = tasks
            })

            EventBus.$on('loadTasks', project_id => {
                this.getProjectTasks(project_id)
            })
        },
        mounted() {
            this.getProject()
            this.getClients()
            this.getTeams()
        }
    }
</script>
