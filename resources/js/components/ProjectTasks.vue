<template>
    <v-container fluid grid-list-md>
            <v-tabs v-model="tabs" fixed-tabs centered color="info" dark slider-color="white">
                <v-tab :href="'#tab-tasks'"><i class="material-icons">list</i> Tasks</v-tab>
                <v-tab :href="'#tab-notes'"><i class="material-icons">edit</i> Notes</v-tab>
                <v-tab :href="'#tab-files'"><i class="material-icons">attach_file</i> Files</v-tab>
                <v-tab :href="'#tab-history'"><i class="material-icons">history</i> History</v-tab>

                <v-tabs-items v-model="tabs">
                    <v-tab-item :value="'tab-tasks'">
                        <v-layout row>
                            <v-container text-xs-center>
                                <v-btn color="info" @click="dialog = true">
                                    <v-icon left dark>add</v-icon>
                                    Add Task
                                </v-btn>
                            </v-container>
                        </v-layout>
                        <v-flex xs12 md6>
                            <v-card>
                                <v-alert :value="true" type="success">
                                    This is just a test.
                                </v-alert>
                                <v-card-text>
                                    This is a proper description if I ever saw one.
                                    This is a proper description if I ever saw one.
                                </v-card-text>
                                <v-card-actions>
                                    <v-flex xs6><i class="material-icons">date_range</i> Start:</v-flex>
                                    <v-flex xs6><i class="material-icons">date_range</i> Due:</v-flex>
                                </v-card-actions>
                                <v-card-actions>
                                    <v-flex xs4>
                                        <v-btn color="success"><i class="material-icons">check</i> Complete</v-btn>
                                    </v-flex>
                                    <v-flex xs4>
                                        <v-btn color="info"><i class="material-icons">edit</i> Edit</v-btn>
                                    </v-flex>
                                    <v-flex xs4>
                                        <v-btn color="red" dark><i class="material-icons">delete</i> Delete</v-btn>
                                    </v-flex>
                                </v-card-actions>
                            </v-card>
                        </v-flex>
                    </v-tab-item>
                    <v-tab-item :value="'tab-notes'">
                        b
                    </v-tab-item>
                    <v-tab-item :value="'tab-files'">
                        c
                    </v-tab-item>
                    <v-tab-item :value="'tab-history'">
                        d
                    </v-tab-item>
                </v-tabs-items>
            </v-tabs>
    </v-container>
</template>

<script>
    export default {
        name: 'ProjectTasks'
    }
</script>

<style scoped>
    .v-card__actions .v-btn {
        width: 95%;
    }
</style>
