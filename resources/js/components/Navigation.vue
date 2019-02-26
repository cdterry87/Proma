<template>
    <div>
        <v-navigation-drawer v-model="drawer" :clipped="$vuetify.breakpoint.lgAndUp" fixed app>
            <v-list dense>
                <template v-for="item in items">
                    <v-list-tile :key="item.text" @click>
                        <v-list-tile-action>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>
                                <router-link :to="{ name: item.route }">{{ item.text }}</router-link>
                            </v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </template>
            </v-list>
        </v-navigation-drawer>
        <v-toolbar app fixed clipped-left dark color="blue darken-3">
            <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
            <v-toolbar-title>Proma</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-text-field flat solo-inverted hide-details prepend-inner-icon="search" label="Search" class="hidden-sm-and-down" ></v-text-field>
            <v-spacer></v-spacer>
            <v-btn icon>
                <v-icon>notifications</v-icon>
            </v-btn>
            <v-btn icon>
                <v-icon>account_circle</v-icon>
            </v-btn>
        </v-toolbar>
    </div>
</template>

<script>
export default {
    name: 'Navigation',
    data() {
        return {
            drawer: null,
            items: [
                { icon: 'home', text: 'Home', route: 'home' },
                { icon: 'person', text: 'Clients', route: 'clients' },
                { icon: 'people', text: 'Teams', route: 'teams' },
            ]
        }
    },
}
</script>

<style scoped>
    aside a {
        height: 100%;
        width: 100%;
        display: block;
    }
</style>
