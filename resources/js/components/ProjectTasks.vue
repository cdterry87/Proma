<template>
    <v-container fluid grid-list-md>
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
        <v-layout row v-if="projectTasks.length == 0">
            There are currently no tasks for this project.
        </v-layout>
        <v-layout row wrap v-else>
            <v-flex xs12 md6 lg4 v-for="task in projectTasks" :key="task.id">
                <v-card class="data-card">
                    <v-alert :value="true" v-if="task.complete" type="success" @click="incompleteTask(task.project_id, task.id)">
                        Task is complete.
                    </v-alert>
                    <v-alert :value="true" v-if="!task.complete" type="warning" @click="completeTask(task.project_id, task.id)">
                        Task is incomplete.
                    </v-alert>
                    <v-card-text>
                        {{ task.description | truncate(100) }}
                    </v-card-text>
                </v-card>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="taskForm" @submit.prevent="addTask">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Task</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="description"></v-textarea>
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
            addTask() {
                let description = this.description
                let project_id = this.projectInfo.id

                axios.post('/api/tasks', { description, project_id })
                .then(response => {
                    this.tasks = this.projectTasks
                    this.tasks.push(response.data.data)

                    let tasks = this.tasks

                    EventBus.$emit('addTask', tasks)
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
