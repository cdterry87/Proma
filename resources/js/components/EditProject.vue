<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container text-xs-right>
                <v-btn color="info" @click="viewProject" small>
                    <v-icon left dark>remove_red_eye</v-icon>
                    View Project
                </v-btn>
            </v-container>
            <v-container>
                <v-form method="POST" id="editProjectForm" @submit.prevent="updateProject">
                    <v-layout row>
                        <v-flex xs12>
                            <v-text-field prepend-icon="work" label="Project Name" v-model="project.name"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex xs12>
                            <v-textarea prepend-icon="notes" label="Description" v-model="project.description"></v-textarea>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat>Save</v-btn>
                        <v-spacer></v-spacer>
                    </v-layout>
                </v-form>
            </v-container>

        </v-card>
    </v-container>
</template>

<script>
    import EventBus from './../eventbus';

    export default {
        name: 'EditProject',
        props: ['projectInfo'],
        methods: {
            getUserData() {
                this.userData = JSON.parse(localStorage.getItem('userData'))
            },
            viewProject() {
                let editProject = false
                eventBus.$emit('editProject', editProject);
            },
            updateProject() {
                let name = this.project.name;
                let description = this.project.description;

                axios.put('/api/projects/' + this.project.id, { name, description })
                .then(response => {
                    this.project = response.data.data
                })
            },
        },
        created() {
            this.getUserData()
        },
        computed: {
            project: {
                get() {
                    return this.projectInfo
                },
                set(value) {
                    return value;
                }
            }
        },
        mounted() {
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt
        }
    }
</script>

<style scoped>

</style>
