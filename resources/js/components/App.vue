<template>
    <div id="app">
        <v-app id="inspire">
            <!-- <v-toolbar app fixed clipped-left dark color="blue darken-3" tabs>
                <v-toolbar-title>Proma</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-text-field flat solo-inverted hide-details prepend-inner-icon="search" label="Search" class="hidden-sm-and-down" ></v-text-field>
                <v-spacer></v-spacer>
                <v-btn icon>
                    <v-icon>notifications</v-icon>
                </v-btn>
                <v-btn icon @click="dialog = true">
                    <v-icon>account_circle</v-icon>
                </v-btn>
            </v-toolbar>
            <template v-slot:extension>
                <v-tabs fixed-tabs color="blue darken-3" slider-color="white" dark>
                    <v-tab>
                        <v-icon>work</v-icon> Projects
                    </v-tab>
                    <v-tab>
                        <v-icon>person</v-icon> Clients
                    </v-tab>
                    <v-tab>
                        <v-icon>people</v-icon> Teams
                    </v-tab>
                </v-tabs>
            </template>
            <v-dialog v-model="dialog" width="500">
                <v-form method="POST" id="userForm" @submit.prevent="updateUser">
                    <v-card>
                        <v-card-title class="grey lighten-4 py-4 title">My Account</v-card-title>
                        <v-container grid-list-sm class="pa-4">
                            <v-layout row wrap>
                                <v-flex xs12>
                                    <v-text-field prepend-icon="person" label="Name" v-model="name"></v-text-field>
                                </v-flex>
                                <v-flex xs12>
                                    <v-text-field prepend-icon="email" label="Email" v-model="email"></v-text-field>
                                </v-flex>
                            </v-layout>
                        </v-container>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn flat color="primary" type="submit">Save</v-btn>
                            <v-btn flat color="primary" form="clientForm" @click="dialog = false">Cancel</v-btn>
                            <v-btn flat color="red" form="clientForm" @click="logout">Sign Out</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-form>
            </v-dialog>
            <v-content>
                <v-container fluid>
                    <v-layout align-center>
                        <v-flex xs12>

                            <v-tabs-items v-model="tabs" class="white elevation-1">
                                <v-tab-item v-for="i in 3" :key="i" :value="'mobile-tabs-5-' + i" >
                                    <v-card-text> Testing </v-card-text>
                                </v-tab-item>
                            </v-tabs-items>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-content> -->

    <v-toolbar
      color="blue darken-3"
      dark
      tabs
    >
        <v-toolbar-title>Proma</v-toolbar-title>
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
          <v-tabs fixed-tabs color="blue darken-3" slider-color="white" dark>
                <v-tab>
                    <v-icon>work</v-icon> Projects
                </v-tab>
                <v-tab>
                    <v-icon>person</v-icon> Clients
                </v-tab>
                <v-tab>
                    <v-icon>people</v-icon> Teams
                </v-tab>
            </v-tabs>
      </template>
    </v-toolbar>

    <v-tabs-items >
      <v-tab-item
        v-for="i in 3"
        :key="i"
        :value="`tab-${i}`"
      >
        <v-card flat>
          <v-card-text>
              Testing
          </v-card-text>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
        </v-app>
    </div>
</template>

<script>
export default {
    data: () => ({
        name: '',
        email: '',
        logout: '',
        tabs: '',
        dialog: false,
        drawer: null,
        items: [
            { icon: 'contacts', text: 'Contacts' },
            { icon: 'history', text: 'Frequently contacted' },
            { icon: 'content_copy', text: 'Duplicates' },
            {
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'Labels',
            model: true,
            children: [
                { icon: 'add', text: 'Create label' }
            ]
            },
            {
            icon: 'keyboard_arrow_up',
            'icon-alt': 'keyboard_arrow_down',
            text: 'More',
            model: false,
            children: [
                { text: 'Import' },
                { text: 'Export' },
                { text: 'Print' },
                { text: 'Undo changes' },
                { text: 'Other contacts' }
            ]
            },
            { icon: 'settings', text: 'Settings' },
            { icon: 'chat_bubble', text: 'Send feedback' },
            { icon: 'help', text: 'Help' },
            { icon: 'phonelink', text: 'App downloads' },
            { icon: 'keyboard', text: 'Go to the old version' }
        ]
    }),
    props: {
        source: String
    }
}
</script>
