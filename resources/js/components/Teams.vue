<template>
    <v-container fluid fill-height>
        <v-layout justify-center align-center>
            Welcome to the Teams Page

            <v-btn color="info" @click="dialog = true">
                <v-icon left dark>add</v-icon>
                Create a team
            </v-btn>
        </v-layout>

        <TeamCreate :dialog="dialog" />
    </v-container>
</template>

<script>
import TeamCreate from './Team/Create';

export default {
    name: 'Teams',
    components: {
        TeamCreate
    },
    data() {
        return {
            dialog: false,
        }
    }
}
</script>
