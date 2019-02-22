<template>
    <v-app id="inspire">
        <v-navigation-drawer v-model="drawer" :clipped="$vuetify.breakpoint.lgAndUp" fixed app>
            <v-list dense>
                <template v-for="item in items">
                    <v-list-group v-if="item.children" :key="item.text" v-model="item.model" :prepend-icon="item.icon" :append-icon="item['icon-alt']">
                        <v-list-tile slot="activator">
                            <v-list-tile-content>
                                <v-list-tile-title>{{ item.text }}</v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>
                        <v-list-tile v-for="(child, i) in item.children" :key="i" @click>
                            <v-list-tile-action v-if="child.icon">
                                <v-icon>{{ child.icon }}</v-icon>
                            </v-list-tile-action>
                            <v-list-tile-content>
                                <v-list-tile-title><router-link :to="{ name: child.route }">{{ child.text }}</router-link></v-list-tile-title>
                            </v-list-tile-content>
                        </v-list-tile>
                    </v-list-group>
                    <v-list-tile v-else :key="item.text" @click>
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
        <v-content>
            <router-view></router-view>
        </v-content>
        <TeamCreate :dialog="dialogTeamCreate" />
        <ClientCreate :dialog="dialogClientCreate" />
    </v-app>
</template>

<script>
import TeamCreate from './Team/Create';
import ClientCreate from './Client/Create';

export default {
  name: "Home",
  components: {
      TeamCreate,
      ClientCreate
  },
  data: () => ({
    dialogTeamCreate: true,
    dialogClientCreate: false,
    drawer: null,
    items: [
        { icon: 'home', text: 'Home', route: 'home' },
        {
            icon: 'person',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Clients',
            model: false,
            children: [
                { icon: 'add', text: 'Add a client', route: 'client' },
            ]
        },
        {
            icon: 'people',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Teams',
            model: false,
            children: [
                { icon: 'add', text: 'Create a team', route: 'team' },
            ]
        },
    ]
  }),
  props: {
    source: String
  }
};
</script>

<style scoped>
    aside a {
        height: 100%;
        width: 100%;
        display: block;
    }
</style>
