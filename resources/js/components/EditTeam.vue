<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container text-xs-right>
                <v-btn color="info" @click="viewTeam">
                    <v-icon left dark>remove_red_eye</v-icon>
                    View Team
                </v-btn>
            </v-container>
            <v-container>
                <v-form method="POST" id="editTeamForm" @submit.prevent="updateTeam">
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

        </v-card>
    </v-container>
</template>

<script>
    import eventBus from './../events';

    export default {
        name: 'EditTeam',
        props: ['teamInfo'],
        methods: {
            getUserData() {
                this.userData = JSON.parse(localStorage.getItem('userData'))
            },
            viewTeam() {
                let editTeam = false
                eventBus.$emit('editTeam', editTeam);
            },
            updateTeam() {
                let name = this.team.name;
                let description = this.team.description;

                axios.put('/api/teams/' + this.team.id, { name, description })
                .then(response => {
                    this.team = response.data.data
                })
            },
        },
        created() {
            this.getUserData()
        },
        computed: {
            team: {
                get() {
                    return this.teamInfo
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
