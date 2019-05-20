<template>
    <v-container fluid grid-list-md>
        <v-layout align-baseline>
            <v-flex xs10>
                <span class="headline">
                    {{ project.name }}
                </span>
            </v-flex>
            <v-flex xs2 text-xs-right>
                <v-btn color="info" @click="editProject" small>
                    <v-icon left dark>edit</v-icon>
                    Edit
                </v-btn>
            </v-flex>
        </v-layout>
        <v-layout row>
            <v-flex xs12>
                {{ project.description }}
            </v-flex>
        </v-layout>
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
                EventBus.$emit('editProject', editProject);
            }
        },
        computed: {
            project() {
                return this.projectInfo
            }
        }
    }
</script>
