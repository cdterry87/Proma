<template>
    <v-container fluid grid-list-md>
        <v-form method="POST" id="editTeamForm" @submit.prevent="editTeam">
            <v-layout row>
                <v-flex xs12>
                    <v-text-field prepend-icon="people" label="Team Name" v-model="name"></v-text-field>
                </v-flex>
            </v-layout>
            <v-layout row>
                <v-flex xs12>
                    <v-textarea prepend-icon="notes" label="Description" v-model="description"></v-textarea>
                </v-flex>
            </v-layout>
            <v-layout row>
                <v-spacer></v-spacer>
                <v-btn type="submit" flat>Save</v-btn>
                <v-btn flat color="primary" form="teamForm" @click="dialog = false">Cancel</v-btn>
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
                name: '',
                description: '',
            }
        },
        methods: {
            editTeam() {

            },
        },
    }
</script>

<style scoped>

</style>
