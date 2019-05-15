<template>
    <v-container fluid grid-list-md>
        <v-container>
            <v-layout align-baseline>
                <v-flex xs6>
                    <span class="headline">
                        <v-icon>people</v-icon> Members
                    </span>
                </v-flex>
                <v-flex xs6 text-xs-right>
                    <v-btn color="info" @click="openDialog" small>
                        <v-icon left dark>add</v-icon>
                        Add Member
                    </v-btn>
                </v-flex>
            </v-layout>

            <v-container v-if="teamMembers.length == 0">
                <v-layout row>
                    There are currently no members on this team.
                </v-layout>
            </v-container>
            <v-container v-else fluid grid-list-md>
                <v-layout row wrap>
                    <v-flex xs12 md6 lg4 v-for="member in teamMembers" :key="member.id">
                        <v-card class="data-card">
                            <v-card-text>
                                <div>
                                    <div class="headline">{{ member.user.name | truncate(20) }}</div>
                                    <span class="grey--text">{{ member.user.email }}</span>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-container>

        <v-dialog v-model="dialog" width="500">
            <v-form method="POST" id="memberForm" @submit.prevent="addMember">
                <v-card>
                    <v-card-title class="grey lighten-4 py-4 title">Add Member</v-card-title>
                    <v-container grid-list-sm class="pa-4">
                        <v-layout row wrap>
                           <v-flex xs12>
                                 <v-autocomplete
                                    :items="users"
                                    item-text="name"
                                    item-value="id"
                                    label="Select a user"
                                    prepend-icon="person"
                                    v-model="user_id"
                                ></v-autocomplete>
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
    import EventBus from './../eventbus'

    export default {
        name: 'TeamMembers',
        props: ['teamInfo', 'teamMembers'],
        data() {
            return {
                dialog: false,
                user_id: '',
                users: []
            }
        },
        methods: {
            openDialog() {
                this.dialog = true;
                this.getUsers();
            },
            getUsers() {
                axios.get('/api/users')
                .then(response => {
                    this.users = response.data
                })
            },
            addMember() {
                let user_id = this.user_id
                let team_id = this.teamInfo.id

                axios.post('/api/members', { user_id, team_id })
                .then(response => {
                    EventBus.$emit('addMember')
                })

                this.reset()
            },
            reset() {
                this.dialog = false
                this.user_id = ''
            }
        },
    }
</script>
