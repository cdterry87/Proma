<template>
    <v-container fluid grid-list-md>
        <v-layout row>
            <v-container text-xs-center>
                <v-btn color="info" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Add a Project
                </v-btn>
            </v-container>
        </v-layout>
        <v-layout row text-xs-center>
            <v-container v-if="projects.length == 0">
                You do not currently have any projects.
            </v-container>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12 md6 lg4 v-for="project in projects" :key="project.id">
                <router-link :to="'/project/' + project.id">
                    <v-card class="data-card">
                        <v-card-text>
                            <div class="headline">
                                {{ project.name | truncate(25) }}
                            </div>
                            {{ project.description | truncate(150) }}
                        </v-card-text>
                    </v-card>
                </router-link>
            </v-flex>
        </v-layout>


        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="projectForm" @submit.prevent="addProject">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Project</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="work" label="Project Name" v-model="name" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="description"></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat>Save</v-btn>
                        <v-btn flat color="primary" form="projectForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </v-container>
</template>

<script>
export default {
    name: 'Projects',
    data() {
        return {
            dialog: false,
            name: '',
            description: '',
            projects: []
        }
    },
    methods: {
        getProjects() {
            axios.get('/api/projects')
            .then(response => {
                this.projects = response.data
            })
        },
        addProject() {
            let name = this.name
            let description = this.description

            axios.post('/api/projects', { name, description })
            .then(response => {
                this.projects.push(response.data.data)
            })

            this.reset()
        },
        reset() {
            this.dialog = false
            this.name = ''
            this.description = ''
        }
    },
    mounted() {
        this.getProjects()
    }

}
</script>
