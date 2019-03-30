<template>
    <v-container fluid grid-list-md>
        <v-layout row>
            <v-container text-xs-center>
                <v-btn color="info" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Create a Project
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
                    <v-card class="editCard">
                        <v-card-text>
                            <div class="headline">
                                {{ project.name | truncate(25) }}
                            </div>
                            {{ project.description | truncate(80) }}
                        </v-card-text>
                    </v-card>
                </router-link>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="teamForm" @submit.prevent="createProject">
                <v-card>
                    <v-card-title class="grey lighten-4 py-4 title">Create Project</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="work" label="Project Name" v-model="name"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-autocomplete
                                    :items="teams"
                                    item-text="name"
                                    item-value="id"
                                    label="Select a team"
                                    prepend-icon="people"
                                    v-model="team_id"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12>
                                 <v-autocomplete
                                    :items="clients"
                                    item-text="name"
                                    item-value="id"
                                    label="Select a client"
                                    prepend-icon="person"
                                    v-model="client_id"
                                ></v-autocomplete>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="description"></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat>Save</v-btn>
                        <v-btn flat color="primary" form="teamForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </v-container>
</template>

<script>
export default {
    name: 'Teams',
    data() {
        return {
            dialog: false,
            name: '',
            description: '',
            team_id: '',
            client_id: '',
            userData: null,
            projects: [],
            teams: [],
            clients: [],
        }
    },
    methods: {
        getUserData() {
            this.userData = JSON.parse(localStorage.getItem('userData'))
        },
        getProjects() {
            console.log('getting projects');
            axios.get('api/projects')
            .then(response => {
                this.projects = response.data
            })
        },
        getClients() {
            axios.get('api/clients')
            .then(response => {
                this.clients = response.data
            })
        },
        getTeams() {
            axios.get('api/teams')
            .then(response => {
                this.teams = response.data
            })
        },
        createProject() {
            let name = this.name
            let description = this.description
            let client_id = this.client_id
            let team_id = this.team_id
            let user_id = this.userData.id

            axios.post('api/projects', { name, description, client_id, team_id, user_id })
            .then(response => {
                this.projects.push(response.data.data)
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
        this.getUserData()
    },
    mounted() {
        axios.defaults.headers.common['Content-Type'] = 'application/json'
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt

        this.getProjects()
        this.getClients()
        this.getTeams()
    },

}
</script>

<style scoped>
.container {
    padding-top: 6px !important;
    padding-bottom: 6px !important;
}
</style>
