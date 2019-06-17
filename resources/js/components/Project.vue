<template>
    <div>
        <v-container fluid grid-list-md>
            <v-layout row>
                <v-flex xs12>
                    <v-card>
                        <v-alert :value="true" v-if="project.completed" type="success" @click="incompleteProject(project.id)" class="clickable">
                            Project completed {{ project.completed_date }}.
                        </v-alert>
                        <v-alert :value="true" v-else-if="!project.completed && project.due_date != '' && project.due_date != null && new Date(project.due_date) < Date.now()" type="error" @click="completeProject(project.id)" class="clickable">
                            Project was due {{ project.due_date }}.
                        </v-alert>
                        <v-alert :value="true" v-else-if="!project.completed" type="warning" @click="completeProject(project.id)" class="clickable">
                            <span v-if="project.due_date">Project is due {{ project.due_date }}.</span>
                            <span v-else>Project is incomplete.</span>
                        </v-alert>
                        <v-card-text>
                             <v-layout align-baseline>
                                <v-flex xs6>
                                    <span class="title">
                                        <v-icon>work</v-icon>
                                        {{ project.name }}
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
                                {{ project.description }}
                            </div>
                            <div class="mt-4" v-if="project.client">
                                <span class="title">
                                    <v-icon>person</v-icon> Client
                                </span>
                                <div>
                                    {{ project.client.name }}
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-flex>
            </v-layout>
        </v-container>

        <ProjectTasks :projectInfo="project" :projectTasks="tasks" />
        <ProjectIssues :projectInfo="project" :projectIssues="issues" />

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
                                :return-value.sync="project.due_date"
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
    </div>
</template>

<script>
    import Event from './../events'
    import ProjectTasks from './ProjectTasks'
    import ProjectIssues from './ProjectIssues'

    export default {
        name: 'Project',
        props: ['id'],
        components: {
            ProjectTasks,
            ProjectIssues,
        },
        data() {
            return {
                dialog: false,
                date_dialog: false,
                project: '',
                tasks: '',
                issues: '',
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

                    this.getTasks(this.id);
                    this.getIssues(this.id);
                })
            },
            getClients() {
                axios.get('/api/clients')
                .then(response => {
                    this.clients = response.data
                })
            },
            getTasks(project_id) {
                axios.get('/api/tasks/' + project_id)
                .then(response => {
                    this.tasks = response.data
                })
            },
            getIssues(project_id) {
                axios.get('/api/projects/' + project_id + '/issues')
                .then(response => {
                    this.issues = response.data
                })
            },
            updateProject() {
                let name = this.project.name
                let description = this.project.description
                let due_date = this.project.due_date
                let client_id = this.project.client_id

                axios.put('/api/projects/' + this.project.id, { name, description, due_date, client_id })
                .then(response => {
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
                    this.getProject()

                    Event.$emit('success', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })
            },
            incompleteProject(project_id) {
                axios.post('/api/projects/' + project_id + '/incomplete')
                .then(response => {
                    this.getProject()

                    Event.$emit('warning', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })
            },
            reset() {
                this.dialog = false
                this.name = ''
                this.client_id = ''
                this.description = ''
            }
        },
        created() {
            Event.$on('loadTasks', project_id => {
                this.getTasks(project_id)
            })
        },
        mounted() {
            this.getProject()
            this.getClients()
        }
    }
</script>
