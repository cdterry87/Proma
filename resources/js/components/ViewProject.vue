<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container text-xs-right>
                <v-btn color="info" @click="editProject">
                    <v-icon left dark>edit</v-icon>
                    Edit Project
                </v-btn>
            </v-container>
            <v-container>
                <v-layout row>
                    <v-flex xs12>
                        <h2>{{ project.name }}</h2>
                    </v-flex>
                </v-layout>
                <v-layout row>
                    <v-flex xs12>
                        {{ project.description }}
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
    </v-container>
</template>

<script>
    import eventBus from './../events';

    export default {
        name: 'ViewProject',
        props: ['projectInfo'],
        methods: {
            editProject() {
                let editProject = true
                eventBus.$emit('editProject', editProject);
            }
        },
        computed: {
            project() {
                return this.projectInfo
            }
        }
    }
</script>

<style scoped>

</style>
