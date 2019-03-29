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
                        <v-btn color="info" @click="dialog = true">
                            <v-icon left dark>add</v-icon>
                            Add Task
                        </v-btn>
                    </v-flex>
                </v-layout>
            </v-container>
            <v-container fluid grid-list-md>
                <v-flex xs12 md6 lg4>
                    <v-card>
                        <v-alert :value="true" type="success">
                            This is just a test.
                        </v-alert>
                        <v-card-text>
                            This is a proper description if I ever saw one.
                            This is a proper description if I ever saw one.
                        </v-card-text>
                        <v-card-actions>
                            <v-flex xs6><i class="material-icons">date_range</i> Start:</v-flex>
                            <v-flex xs6><i class="material-icons">date_range</i> Due:</v-flex>
                        </v-card-actions>
                        <v-card-actions>
                            <v-flex xs4 mr-1 ml-1>
                                <v-btn color="success" block small><i class="material-icons">check</i> Complete</v-btn>
                            </v-flex>
                            <v-flex xs4 mr-1 ml-1>
                                <v-btn color="info" block small><i class="material-icons">edit</i> Edit</v-btn>
                            </v-flex>
                            <v-flex xs4 mr-1 ml-1>
                                <v-btn color="red" dark block small><i class="material-icons">delete</i> Delete</v-btn>
                            </v-flex>
                        </v-card-actions>
                    </v-card>
                </v-flex>
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
    export default {
        name: 'ProjectTasks',
        props: ['projectInfo'],
        data() {
            return {
                dialog: false,
                description: '',
                start_date: '',
                due_date: '',
                userData: null,
                tasks: []
            }
        },
        methods: {
            getUserData() {
                this.userData = JSON.parse(localStorage.getItem('userData'))
            },
            getTasks() {
                axios.get('/api/tasks')
                .then(response => {
                    this.tasks = response.data
                    console.log('tasks', this.tasks);
                })
            },
            createTask() {
                let description = this.description;
                let project_id = this.projectInfo.id

                axios.post('/api/tasks', { description, project_id })
                .then(response => {
                    this.tasks.push(response.data.data)
                })

                this.reset()
            },
            reset() {
                this.dialog = false
                this.description = ''
            }
        },
        created() {
            this.getUserData()
        },
        mounted() {
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt

            this.getTasks()
        }
    }
</script>
