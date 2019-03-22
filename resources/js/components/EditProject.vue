<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container>
                <h2>Edit Project</h2>
                <v-form method="POST" id="editProjectForm" @submit.prevent="updateProject">
                    <v-layout row>
                        <v-flex xs12>
                            <v-text-field prepend-icon="person" label="Project Name" v-model="project.name"></v-text-field>
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
    export default {
        name: 'EditProject',
        props: ['id'],
        data() {
            return {
                project: ''
            }
        },
        methods: {
            getUserData() {
                this.userData = JSON.parse(localStorage.getItem('userData'))
            },
            getProject() {
                axios.get('/api/projects/' + this.id)
                .then(response => {
                    this.project = response.data
                })
            },
            updateProject() {
                let name = this.project.name;
                let description = this.project.description;
                let team_id = this.project.team_id;
                let client_id = this.project.client_id;

                axios.put('/api/projects/' + this.id, { name, description, team_id, client_id })
                .then(response => {
                    this.project = response.data.data
                })
            },
        },
        created() {
            this.getUserData()
        },
        mounted() {
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt

            this.getProject()
        }
    }
</script>

<style scoped>

</style>
