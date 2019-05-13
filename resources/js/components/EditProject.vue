<template>
    <v-container fluid grid-list-md>
        <v-card>
            <v-container text-xs-right>
                <v-btn color="info" @click="viewProject" small>
                    <v-icon left dark>remove_red_eye</v-icon>
                    View Project
                </v-btn>
            </v-container>
            <v-container>
                <v-form method="POST" id="editProjectForm" @submit.prevent="updateProject">
                    <v-layout row>
                        <v-flex xs12>
                            <v-text-field prepend-icon="work" label="Project Name" v-model="project.name" maxlength="100"></v-text-field>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex xs12>
                            <v-textarea prepend-icon="notes" label="Description" v-model="project.description"></v-textarea>
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
    import EventBus from './../eventbus';

    export default {
        name: 'EditProject',
        props: ['projectInfo'],
        methods: {
            viewProject() {
                let editProject = false
                EventBus.$emit('editProject', editProject);
            },
            updateProject() {
                let name = this.project.name;
                let description = this.project.description;

                axios.put('/api/projects/' + this.project.id, { name, description })
                .then(response => {
                    this.project = response.data.data
                })
            },
        },
        computed: {
            project: {
                get() {
                    return this.projectInfo
                },
                set(value) {
                    return value;
                }
            }
        },
    }
</script>

<style scoped>

</style>
