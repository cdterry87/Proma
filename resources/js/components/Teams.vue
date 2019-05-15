<template>
    <v-container fluid grid-list-md>
        <v-layout row>
            <v-container text-xs-center>
                <v-btn color="info" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Create a Team
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
                <router-link :to="'/team/' + team.id">
                    <v-card class="data-card">
                        <v-card-text>
                            <div class="headline">
                                {{ team.name | truncate(25) }}
                            </div>
                            {{ team.description | truncate(150) }}
                        </v-card-text>
                    </v-card>
                </router-link>
            </v-flex>
        </v-layout>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="teamForm" @submit.prevent="createTeam">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Create Team</v-card-title>
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
        createTeam() {
            let name = this.name;
            let description = this.description;

            axios.post('/api/teams', { name, description })
            .then(response => {
                this.teams.push(response.data.data)
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
        this.getTeams()
    }

}
</script>

<style scoped>

</style>
