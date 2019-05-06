<template>
    <div id="app">
        <v-app class="inspire">
            <v-toolbar color="blue darken-3" dark tabs >
                <v-toolbar-title>
                    <a href="/">Proma</a>
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-text-field flat solo-inverted hide-details prepend-inner-icon="search" label="Search" class="hidden-sm-and-down" ></v-text-field>
                <v-spacer></v-spacer>
                <v-btn icon>
                    <v-icon>notifications</v-icon>
                </v-btn>
                <v-btn icon @click="dialog = true">
                    <v-icon>account_circle</v-icon>
                </v-btn>

                <template v-slot:extension>
                    <v-tabs v-model="tabs" centered color="blue darken-3" slider-color="white" fixed-tabs grow >
                        <v-tab :href="`#projects`" >
                            <v-icon>work</v-icon>
                            <span class="tab-title">Projects</span>
                        </v-tab>
                        <v-tab :href="`#clients`" >
                            <v-icon>person</v-icon>
                            <span class="tab-title">Clients</span>
                        </v-tab>
                        <v-tab :href="`#teams`" >
                            <v-icon>people</v-icon>
                            <span class="tab-title">Teams</span>
                        </v-tab>
                    </v-tabs>
                </template>
            </v-toolbar>
            <v-tabs-items v-model="tabs">
                <v-tab-item :value="`projects`">
                    <h2>These are my Projects</h2>
                </v-tab-item>
                <v-tab-item :value="`clients`">
                    <h2>These are my Clients</h2>
                </v-tab-item>
                <v-tab-item :value="`teams`">
                    <h2>These are my Teams</h2>
                </v-tab-item>
            </v-tabs-items>
        </v-app>
    </div>
</template>

<script>
export default {
   name: 'App',
   data() {
       return {
           tabs: 'projects',
       }
   },
}
</script>

<style>
nav.v-toolbar a{
    color: inherit;
    text-decoration: none;
}

span.tab-title {
    margin-left: 5px;
    margin-top: 5px;
}
</style>
