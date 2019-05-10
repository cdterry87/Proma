<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container text-xs-right>
                <v-btn color="info" @click="editTeam" small>
                    <v-icon left dark>edit</v-icon>
                    Edit Team
                </v-btn>
            </v-container>
            <v-container>
                <v-layout row>
                    <v-flex xs12 class="headline">
                        {{ team.name }}
                    </v-flex>
                </v-layout>
                <v-layout row>
                    <v-flex xs12>
                        {{ team.description }}
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
    </v-container>
</template>

<script>
    import EventBus from './../eventbus';

    export default {
        name: 'ViewTeam',
        props: ['teamInfo'],
        methods: {
            editTeam() {
                let editTeam = true
                EventBus.$emit('editTeam', editTeam);
            }
        },
        computed: {
            team() {
                return this.teamInfo
            }
        }
    }
</script>

<style scoped>

</style>
