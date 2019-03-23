<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container text-xs-right>
                <v-btn color="info" @click="editTeam">
                    <v-icon left dark>edit</v-icon>
                    Edit Team
                </v-btn>
            </v-container>
            <v-container>
                <v-layout row>
                    <v-flex xs12>
                        <h2>{{ team.name }}</h2>
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
    import eventBus from './../events';

    export default {
        name: 'ViewTeam',
        props: ['team'],
        methods: {
            editTeam() {
                let editTeam = true
                eventBus.$emit('editTeam', editTeam);
            }
        }
    }
</script>

<style scoped>

</style>
