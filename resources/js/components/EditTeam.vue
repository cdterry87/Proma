<template>
    <v-container fluid grid-list-md>
        <h2>Edit Team</h2>
        <v-form method="POST" id="editTeamForm" @submit.prevent="editTeam">
            <v-layout row>
                <v-flex xs12>
                    <v-text-field prepend-icon="people" label="Team Name" v-model="team.name"></v-text-field>
                </v-flex>
            </v-layout>
            <v-layout row>
                <v-flex xs12>
                    <v-textarea prepend-icon="notes" label="Description" v-model="team.description"></v-textarea>
                </v-flex>
            </v-layout>
            <v-layout row>
                <v-spacer></v-spacer>
                <v-btn type="submit" flat>Save</v-btn>
                <v-spacer></v-spacer>
            </v-layout>
        </v-form>
    </v-container>
</template>

<script>
    export default {
        name: 'EditTeam',
        props: ['id'],
        data() {
            return {
                team: ''
            }
        },
        methods: {
            getUserData() {
                this.userData = JSON.parse(localStorage.getItem('userData'))
            },
            getTeam() {
                axios.get('/api/teams/' + this.id)
                .then(response => {
                    this.team = response.data
                })
            },
            editTeam() {
                let team_id = this.team.team_id;
                let name = this.team.name;
                let description = this.team.description;

                axios.put('/api/teams/' + this.id, { team_id, name, description })
                .then(response => {
                    this.team = response.data.data
                })
            },
        },
        created() {
            this.getUserData()
        },
        mounted() {
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt

            this.getTeam()
        }
    }
</script>

<style scoped>

</style>
