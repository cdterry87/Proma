<template>
    <div id="app">
        <v-app id="inspire">
            <v-toolbar app fixed clipped-left dark color="blue darken-3">
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
                <v-container fluid fill-height>
                    <v-layout justify-center align-center>
                        Test
                    </v-layout>
                </v-container>
            </v-content>
        </v-app>
    </div>
</template>

<script>
  export default {
    data: () => ({
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
