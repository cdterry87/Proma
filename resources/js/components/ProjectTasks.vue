<template>
    <v-container fluid grid-list-md>
        <v-layout row>
            <v-container text-xs-center>
                <v-btn color="info" @click="dialog = true">
                    <v-icon left dark>add</v-icon>
                    Add Task
                </v-btn>
            </v-container>
        </v-layout>
        <v-container>
            <v-tabs v-model="tabs" fixed-tabs centered color="info" dark slider-color="white">
                <v-tab :href="'#tab-tasks'"><i class="material-icons">list</i> Tasks</v-tab>
                <v-tab :href="'#tab-notes'"><i class="material-icons">edit</i> Notes</v-tab>
                <v-tab :href="'#tab-files'"><i class="material-icons">attach_file</i> Files</v-tab>
                <v-tab :href="'#tab-history'"><i class="material-icons">history</i> History</v-tab>
            </v-tabs>
            <v-tabs-items v-model="tabs">
                <v-tab-item :value="'tab-tasks'">
                    <v-flex xs12 md6 lg4 v-for="project in projects" :key="project.id">
                        <router-link :to="'/project/' + project.id">
                            <v-card class="editCard">
                                <v-card-text>
                                    <div class="headline">
                                        {{ project.name | truncate(25) }}
                                    </div>
                                    {{ project.description | truncate(80) }}
                                </v-card-text>
                            </v-card>
                        </router-link>
                    </v-flex>
                </v-tab-item>
                <v-tab-item :value="'tab-notes'">
                </v-tab-item>
                <v-tab-item :value="'tab-files'">
                </v-tab-item>
                <v-tab-item :value="'tab-history'">
                </v-tab-item>
            </v-tabs-items>
        </v-container>
    </v-container>
</template>

<script>
    export default {
        name: 'ProjectTasks'
    }
</script>

<style scoped>

</style>
