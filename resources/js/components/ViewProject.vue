<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container>
                <v-layout align-baseline>
                    <v-flex xs6>
                        <span class="headline">
                            <v-icon>work</v-icon> {{ project.name }}
                        </span>
                    </v-flex>
                    <v-flex xs6 text-xs-right>
                        <v-btn color="info" @click="editProject" small>
                            <v-icon left dark>edit</v-icon>
                            Edit Project
                        </v-btn>
                    </v-flex>
                </v-layout>
            </v-container>
            <v-container>
                <v-layout row>
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
    import EventBus from './../eventbus';

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
