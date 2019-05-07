<template>
    <v-container fluid grid-list-md>
        <v-layout row wrap>
            <v-flex xs12 md6 lg4>
                <router-link to="">
                    <v-card class="editCard">
                        <v-card-text>
                            <div class="headline">Project Name</div>
                            This is just a very short description of the project.
                        </v-card-text>
                    </v-card>
                </router-link>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    export default {
        name: 'Projects'
    }
</script>

<style lang="scss" scoped>

</style>
