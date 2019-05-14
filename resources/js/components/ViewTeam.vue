<template>
    <v-container fluid grid-list-md>
        <v-container>
            <v-layout align-baseline>
                <v-flex xs10>
                    <span class="headline">
                        {{ team.name }}
                    </span>
                </v-flex>
                <v-flex xs2 text-xs-right>
                    <v-btn color="info" @click="editTeam" small>
                        <v-icon left dark>edit</v-icon>
                        Edit
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-container>
        <v-container>
            <v-layout row>
                <v-flex xs12>
                    {{ team.description }}
                </v-flex>
            </v-layout>
        </v-container>
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
