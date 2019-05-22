<template>
    <div>
        <v-container fluid grid-list-md>
            <v-layout align-baseline>
                <v-flex xs10>
                    <span class="headline">
                        {{ team.name }}
                    </span>
                </v-flex>
                <v-flex xs2 text-xs-right>
                    <v-btn color="info" @click="dialog = true" small>
                        <v-icon left dark>edit</v-icon>
                        Edit
                    </v-btn>
                </v-flex>
            </v-layout>
            <v-layout row>
                <v-flex xs12>
                    {{ team.description }}
                </v-flex>
            </v-layout>
        </v-container>

        <TeamMembers :teamInfo="team" :teamMembers="members" />

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="editTeamForm" @submit.prevent="updateTeam">
                <v-card>
                    <v-card-title class="blue darken-3 white--text py-4 title">Add Team</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field prepend-icon="people" label="Team Name" v-model="team.name" maxlength="100"></v-text-field>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea prepend-icon="notes" label="Description" v-model="team.description"></v-textarea>
                            </v-flex>
                        </v-layout>
                    </v-container>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn type="submit" color="blue darken-2" flat>Save</v-btn>
                        <v-btn flat color="red darken-2" form="editTeamForm" @click="dialog = false">Cancel</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
        <v-snackbar v-model="snackbar.enabled" :color="snackbar.color" :bottom="true" :right="true" :timeout="snackbar.timeout">
            {{ snackbar.message }}
            <v-btn color="white" flat @click="snackbar.enabled = false"><v-icon>close</v-icon></v-btn>
        </v-snackbar>
    </div>
</template>

<script>
    import EventBus from './../eventbus'
    import TeamMembers from './TeamMembers'

    export default {
        name: 'Team',
        props: ['id'],
        components: {
            TeamMembers
        },
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
                team: '',
                members: ''
            }
        },
        methods: {
            getTeam() {
                axios.get('/api/teams/' + this.id)
                .then(response => {
                    this.team = response.data

                    this.getTeamMembers(this.id);
                })
            },
            getTeamMembers(team_id) {
                axios.get('/api/members/' + team_id)
                .then(response => {
                    this.members = response.data
                })
            },
            updateTeam() {
                let name = this.team.name;
                let description = this.team.description;

                axios.put('/api/teams/' + this.team.id, { name, description })
                .then(response => {
                    // this.team = response.data.data

                    this.snackbar.color = 'success'
                    this.snackbar.message = "Team updated successfully!"
                    this.snackbar.enabled = true
                })
                .catch(function (error) {
                    this.snackbar.color = 'error'
                    this.snackbar.message = "Error updating team!"
                    this.snackbar.enabled = true
                })

                this.reset()
            },
            reset() {
                this.dialog = false
                this.name = ''
                this.description = ''
            }
        },
        created() {
            EventBus.$on('addMember', () => {
                this.getTeamMembers(this.id)
            })

            EventBus.$on('loadMembers', team_id => {
                this.getTeamMembers(team_id)
            })
        },
        mounted() {
            this.getTeam()
        }
    }
</script>
