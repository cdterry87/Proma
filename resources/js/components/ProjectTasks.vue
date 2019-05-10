<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container>
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
            </v-container>
            <v-container v-if="projectTasks.length == 0">
                <v-layout row>
                    There are currently no tasks for this project.
                </v-layout>
            </v-container>
            <v-container v-else fluid grid-list-md>
                <v-layout row wrap>
                    <v-flex xs12 md6 lg4 v-for="task in projectTasks" :key="task.id">
                        <v-card>
                            <v-alert :value="true" v-if="task.complete" type="success">
                                Task is complete.
                            </v-alert>
                            <v-alert :value="true" v-if="!task.complete" type="warning">
                                Task is incomplete.
                            </v-alert>
                            <v-card-text>
                                <v-layout>
                                    <v-flex>
                                        {{ task.description }}
                                    </v-flex>
                                </v-layout>
                                <v-layout row>
                                    <v-flex xs6>
                                        <div class="caption">
                                            Start Date:
                                        </div>
                                    </v-flex>
                                    <v-flex xs6>
                                        <div class="caption">
                                            Due Date:
                                        </div>
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                            <v-card-actions>
                                <v-flex xs6 mr-1 ml-1 v-if="!task.complete">
                                    <v-btn color="success" block small @click="completeTask(task.project_id, task.id)"><i class="material-icons">check</i> Complete</v-btn>
                                </v-flex>
                                <v-flex xs6 mr-1 ml-1 v-else>
                                    <v-btn color="warning" block small @click="incompleteTask(task.project_id, task.id)"><i class="material-icons">warning</i> Incomplete</v-btn>
                                </v-flex>
                                <v-flex xs6 mr-1 ml-1>
                                    <v-btn color="info" block small><i class="material-icons">edit</i> Edit</v-btn>
                                </v-flex>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="taskForm" @submit.prevent="createTask">
                <v-card>
                    <v-card-title class="grey lighten-4 py-4 title">Create Task</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-textarea prepend-icon="list" label="Description" v-model="description"></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat>Save</v-btn>
                        <v-btn flat color="primary" form="taskForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
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
                description: '',
                start_date: '',
                due_date: '',
            }
        },
        methods: {
            createTask() {
                let description = this.description
                let project_id = this.projectInfo.id

                axios.post('/api/tasks', { description, project_id })
                .then(response => {
                    this.tasks = this.projectTasks
                    this.tasks.push(response.data.data)

                    let tasks = this.tasks

                    EventBus.$emit('createTask', tasks)
                })

                this.reset()
            },
            completeTask(project_id, task_id) {
                axios.post('/api/tasks/' + project_id + '/complete/' + task_id)
                .then(response => {
                    EventBus.$emit('loadTasks', project_id)
                })
            },
            incompleteTask(project_id, task_id) {
                axios.post('/api/tasks/' + project_id + '/incomplete/' + task_id)
                .then(response => {
                    EventBus.$emit('loadTasks', project_id)
                })
            },
            reset() {
                this.dialog = false
                this.description = ''
            }
        },
    }
</script>
