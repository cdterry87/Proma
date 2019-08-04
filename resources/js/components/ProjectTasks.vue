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
                                <v-btn color="primary" @click="addTask" small>
                                    <v-icon left dark>add</v-icon>
                                    Add Task
                                </v-btn>
                            </v-flex>
                        </v-layout>

                        <v-content v-if="projectTasks">
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
                                :items="projectTasks"
                                :search="search"
                                :pagination.sync="pagination"
                                hide-actions
                                no-data-text="This project does not currently have any tasks."
                            >
                                <template v-slot:items="props">
                                    <td>
                                        <span v-if="parseInt(props.item.completed)">
                                            <v-icon class="pointer" color="success" @click="incompleteTask(props.item.project_id, props.item.id)">check_circle</v-icon>
                                        </span>
                                        <span v-else-if="!parseInt(props.item.completed) && props.item.due_date != '' && props.item.due_date != null && new Date(props.item.due_date) < Date.now()">
                                            <v-icon class="pointer" color="error" @click="completeTask(props.item.project_id, props.item.id)">warning</v-icon>
                                        </span>
                                        <span v-else-if="!parseInt(props.item.completed)">
                                            <v-icon class="pointer" color="warning" @click="completeTask(props.item.project_id, props.item.id)">remove_circle</v-icon>
                                        </span>
                                    </td>
                                    <td>{{ props.item.description | truncate(150) }}</td>
                                    <td width="15%">{{ props.item.due_date | fromNow() }}</td>
                                    <td width="25%">
                                        <v-form method="POST" id="deleteForm" @submit.prevent="deleteTask(props.item.project_id, props.item.id)">
                                            <v-btn small color="primary" class="white--text" @click="editTask(props.item)">Edit</v-btn>
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
            <v-form method="POST" id="taskForm" @submit.prevent="saveTask" ref="form" lazy-validation>
                <input type="hidden" name="task_id" v-model="task_id">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Save Task</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="description" :rules="[v => !!v || 'Description is required']" required></v-textarea>
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
    </v-container>
</template>

<script>
    import Event from './../events'

    export default {
        name: 'ProjectTasks',
        props: ['projectInfo', 'projectTasks'],
        data() {
            return {
                dialog: false,
                date_dialog: false,
                description: '',
                due_date: '',
                task_id: '',
                search: '',
                pagination: {
                    sortBy: 'completed',
                    rowsPerPage: -1
                },
                headers: [
                    { text: 'Status', value: 'completed' },
                    { text: 'Description', value: 'description' },
                    { text: 'Due Date', value: 'due_date' },
                    { text: 'Actions', value: 'actions', sortable: false },
                ],
            }
        },
        methods: {
            addTask() {
                this.dialog = true

                this.due_date = ''
                this.description = ''
                this.task_id = ''
            },
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
                if (this.$refs.form.validate()) {
                    let due_date = this.due_date
                    let description = this.description
                    let project_id = this.projectInfo.id
                    let task_id = this.task_id

                    let url = '/api/contacts'

                    let method = 'post'
                    if (_.isNumber(task_id)) {
                        method = 'put'
                        url += '/' + task_id
                    }


                    axios({
                        method: method,
                        url: url,
                        data: { due_date, description, project_id }
                    })
                    .then(response => {
                        Event.$emit('loadTasks', project_id)

                        Event.$emit('success', response.data.message)
                    })
                    .catch(function (error) {
                        Event.$emit('error', response.data.message)
                    })

                    this.reset()
                }
            },
            completeTask(project_id, task_id) {
                axios.post('/api/tasks/' + project_id + '/complete/' + task_id)
                .then(response => {
                    Event.$emit('loadTasks', project_id)

                    Event.$emit('success', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })
            },
            incompleteTask(project_id, task_id) {
                axios.post('/api/tasks/' + project_id + '/incomplete/' + task_id)
                .then(response => {
                    Event.$emit('loadTasks', project_id)

                    Event.$emit('warning', response.data.message)
                })
                .catch(function (error) {
                    Event.$emit('error', response.data.message)
                })
            },
            deleteTask(project_id, task_id) {
                axios.delete('/api/tasks/' + task_id)
                .then(response => {
                    Event.$emit('loadTasks', project_id)

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
