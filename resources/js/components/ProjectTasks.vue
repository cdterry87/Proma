<template>
    <v-container fluid grid-list-md>
        <v-layout align-baseline>
            <v-flex xs12>
                <v-card>
                    <v-card-text>
                        <v-layout align-baseline>
                            <v-flex xs6>
                                <span class="headline">
                                    <v-icon>list</v-icon> Tasks
                                </span>
                            </v-flex>
                            <v-flex xs6 text-xs-right>
                                <v-btn color="info" @click="dialog = true" small>
                                    <v-icon left dark>add</v-icon>
                                    Add Task
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>
        <v-layout row v-if="projectTasks.length == 0">
            There are currently no tasks for this project.
        </v-layout>
        <v-layout row wrap v-else>
            <v-flex xs12 md6 lg4 v-for="task in projectTasks" :key="task.id">
                <v-card class="data-card medium">
                    <v-alert :value="true" v-if="task.complete" type="success" @click="incompleteTask(task.project_id, task.id)">
                        Task completed {{ task.completed_date }}.
                    </v-alert>
                    <v-alert :value="true" v-else-if="!task.complete && task.due_date != '' && task.due_date != null && new Date(task.due_date) < Date.now()" type="error" @click="completeTask(task.project_id, task.id)">
                        Task was due {{ task.due_date }}.
                    </v-alert>
                    <v-alert :value="true" v-else-if="!task.complete" type="warning" @click="completeTask(task.project_id, task.id)">
                        <span v-if="task.due_date">Task is due {{ task.due_date }}.</span>
                        <span v-else>Task is incomplete.</span>
                    </v-alert>
                    <v-card-text @click="editTask(task)">
                        {{ task.description | truncate(100) }}
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn flat color="red darken-2" @click="removeTask(task.project_id, task.id)">Remove</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="taskForm" @submit.prevent="saveTask">
                <input type="hidden" name="task_id" v-model="task_id">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Save Task</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
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
                        <v-btn flat color="red darken-2" form="taskForm" @click="dialog = false">Cancel</v-btn>
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
    import EventBus from './../eventbus';

    export default {
        name: 'ProjectTasks',
        props: ['projectInfo', 'projectTasks'],
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
                description: '',
                due_date: '',
                task_id: ''
            }
        },
        methods: {
            editTask(task) {
                this.dialog = true

                this.due_date = task.due_date
                this.description = task.description
                this.task_id = task.id
            },
            dueDate(due_date) {
                this.date_dialog = false
                this.$refs.datePicker.save(due_date)
            },
            saveTask() {
                let due_date = this.due_date
                let description = this.description
                let project_id = this.projectInfo.id

                let task_id = this.task_id

                let method = (!_.isNumber(task_id) ? 'post' : 'put')

                axios({
                    method: method,
                    url: '/api/tasks/' + task_id,
                    data: { due_date, description, project_id }
                })
                .then(response => {
                    EventBus.$emit('loadTasks', project_id)

                    this.snackbar.color = 'success'
                    this.snackbar.message = "Task added successfully!"
                    this.snackbar.enabled = true
                })
                .catch(function (error) {
                    this.snackbar.color = 'error'
                    this.snackbar.message = "Error adding task!"
                    this.snackbar.enabled = true
                })

                this.reset()
            },
            completeTask(project_id, task_id) {
                axios.post('/api/tasks/' + project_id + '/complete/' + task_id)
                .then(response => {
                    EventBus.$emit('loadTasks', project_id)

                    this.snackbar.color = 'success'
                    this.snackbar.message = "Task is now complete!"
                    this.snackbar.enabled = true
                })
                .catch(function (error) {
                    this.snackbar.color = 'danger'
                    this.snackbar.message = "Task could not be completed!"
                    this.snackbar.enabled = true
                })
            },
            incompleteTask(project_id, task_id) {
                axios.post('/api/tasks/' + project_id + '/incomplete/' + task_id)
                .then(response => {
                    EventBus.$emit('loadTasks', project_id)

                    this.snackbar.color = 'warning'
                    this.snackbar.message = "Task is now incomplete!"
                    this.snackbar.enabled = true
                })
                .catch(function (error) {
                    this.snackbar.color = 'danger'
                    this.snackbar.message = "Task could not be changed to incomplete!"
                    this.snackbar.enabled = true
                })
            },
            removeTask(project_id, task_id) {
                axios.delete('/api/tasks/' + task_id)
                .then(response => {
                    EventBus.$emit('loadTasks', project_id)

                    this.snackbar.color = 'success'
                    this.snackbar.message = "Task successfully removed!"
                    this.snackbar.enabled = true
                })
            },
            reset() {
                this.dialog = false
                this.description = ''
            }
        },
    }
</script>
