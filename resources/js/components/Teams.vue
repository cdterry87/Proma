<template>
    <v-container fluid grid-list-md>
        <v-layout row>
            <v-container text-xs-center>
                <v-btn color="info" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Add a Team
                </v-btn>
            </v-container>
        </v-layout>
        <v-layout row text-xs-center>
            <v-container v-if="teams.length == 0">
                You do not currently have any teams.
            </v-container>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs12 md6 lg4 v-for="team in teams" :key="team.id">
                <v-card class="data-card medium">
                    <router-link :to="'/team/' + team.id">
                        <v-card-text>
                            <div class="headline">
                                {{ team.name | truncate(25) }}
                            </div>
                            {{ team.description | truncate(150) }}
                        </v-card-text>
                    </router-link>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn flat color="red darken-2" @click="removeTeam(team.id)">Remove</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="teamForm" @submit.prevent="addTeam">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Team</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="people" label="Team Name" v-model="name" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="description"></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" flat color="blue darken-2">Save</v-btn>
                        <v-btn flat color="red darken-2" form="teamForm" @click="dialog = false">Cancel</v-btn>
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

export default {
    name: 'Teams',
    data() {
        return {
            dialog: false,
            snackbar: {
                enabled: false,
                message: '',
                timeout: 5000,
                y: 'bottom',
                x: 'right',
                color: ''
            },
            name: '',
            description: '',
            teams: []
        }
    },
    methods: {
        getTeams() {
            axios.get('/api/teams')
            .then(response => {
                this.teams = response.data
            })
        },
        addTeam() {
            let name = this.name;
            let description = this.description;

            axios.post('/api/teams', { name, description })
            .then(response => {
                this.teams.push(response.data.data)

                this.snackbar.color = 'success'
                this.snackbar.message = "Team successfully created!"
                this.snackbar.enabled = true
            })
            .catch(function (error) {
                this.snackbar.color = 'error'
                this.snackbar.message = "Error creating team!"
                this.snackbar.enabled = true
            })

            this.reset()
        },
        removeTeam(team_id) {
            axios.delete('/api/teams/' + team_id)
            .then(response => {
                this.getTeams()

                this.snackbar.color = 'success'
                this.snackbar.message = "Team successfully removed!"
                this.snackbar.enabled = true
            })
        },
        reset() {
            this.dialog = false
            this.name = ''
            this.description = ''
        }
    },
    mounted() {
        this.getTeams()
    }

}
</script>

<style scoped>

</style>
