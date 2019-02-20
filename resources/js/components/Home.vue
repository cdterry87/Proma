<template>
    <v-app id="inspire">
        <v-navigation-drawer v-model="drawer" :clipped="$vuetify.breakpoint.lgAndUp" fixed app>
            <v-list dense>
                <template v-for="item in items">
                <v-layout v-if="item.heading" :key="item.heading" row align-center>
                    <v-flex xs6>
                    <v-subheader v-if="item.heading">{{ item.heading }}</v-subheader>
                    </v-flex>
                    <v-flex xs6 class="text-xs-center">
                    <a href="#!" class="body-2 black--text">EDIT</a>
                    </v-flex>
                </v-layout>
                <v-list-group v-else-if="item.children" :key="item.text" v-model="item.model" :prepend-icon="item.model ? item.icon : item['icon-alt']" append-icon >
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
                        <v-list-tile-title>{{ child.text }}</v-list-tile-title>
                    </v-list-tile-content>
                    </v-list-tile>
                </v-list-group>
                <v-list-tile v-else :key="item.text" @click>
                    <v-list-tile-action>
                    <v-icon>{{ item.icon }}</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                    <v-list-tile-title>{{ item.text }}</v-list-tile-title>
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
                <v-icon>apps</v-icon>
            </v-btn>
            <v-btn icon>
                <v-icon>notifications</v-icon>
            </v-btn>
            <v-btn icon>
                <v-icon>account_circle</v-icon>
            </v-btn>
        </v-toolbar>
        <v-content>
            <v-container fluid fill-height>
                <v-layout justify-center align-center>
                    Welcome to the Home Page
                </v-layout>
            </v-container>
        </v-content>
        <v-dialog v-model="dialog" width="800px">
            <v-card>
                <v-card-title class="grey lighten-4 py-4 title">Create contact</v-card-title>
                <v-container grid-list-sm class="pa-4">
                <v-layout row wrap>
                    <v-flex xs12 align-center justify-space-between>
                    <v-layout align-center>
                        <v-avatar size="40px" class="mr-3">
                        <img src="//ssl.gstatic.com/s2/oz/images/sge/grey_silhouette.png" alt>
                        </v-avatar>
                        <v-text-field placeholder="Name"></v-text-field>
                    </v-layout>
                    </v-flex>
                    <v-flex xs6>
                    <v-text-field prepend-icon="business" placeholder="Company"></v-text-field>
                    </v-flex>
                    <v-flex xs6>
                    <v-text-field placeholder="Job title"></v-text-field>
                    </v-flex>
                    <v-flex xs12>
                    <v-text-field prepend-icon="mail" placeholder="Email"></v-text-field>
                    </v-flex>
                    <v-flex xs12>
                    <v-text-field type="tel" prepend-icon="phone" placeholder="(000) 000 - 0000" mask="phone" ></v-text-field>
                    </v-flex>
                    <v-flex xs12>
                    <v-text-field prepend-icon="notes" placeholder="Notes"></v-text-field>
                    </v-flex>
                </v-layout>
                </v-container>
                <v-card-actions>
                    <v-btn flat color="primary">More</v-btn>
                    <v-spacer></v-spacer>
                    <v-btn flat color="primary" @click="dialog = false">Cancel</v-btn>
                    <v-btn flat @click="dialog = false">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-app>
</template>

<script>
export default {
  name: "Home",
  data: () => ({
    dialog: false,
    drawer: null,
    items: [
      { icon: "person", text: "Clients" },
      { icon: "people", text: "Team" },
      { icon: "view_week", text: "Projects" },
      { icon: "settings", text: "Settings" },
      { icon: "help", text: "Help" },
    ]
  }),
  props: {
    source: String
  }
};
</script>
