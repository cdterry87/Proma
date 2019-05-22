<template>
    <div>
        <v-container fluid grid-list-md>
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
            <v-layout row>
                <v-flex xs12>
                    {{ project.description }}
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
