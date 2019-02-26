<template>
    <v-app id="inspire">
        <Navigation />
        <v-content>
            <router-view></router-view>
        </v-content>
        <TeamCreate :dialog="dialogTeamCreate" />
        <ClientCreate :dialog="dialogClientCreate" />
    </v-app>
</template>

<script>
import Navigation from './Navigation';
import TeamCreate from './Team/Create';
import ClientCreate from './Client/Create';

export default {
    name: "Home",
    components: {
        Navigation,
        TeamCreate,
        ClientCreate
    },
    data: () => ({
        dialogTeamCreate: false,
        dialogClientCreate: false,
    }),
    props: {
        source: String
    }
};
</script>
