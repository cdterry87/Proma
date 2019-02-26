<template>
    <v-app id="inspire">
        <Navigation />
        <v-content>
            <router-view></router-view>
        </v-content>
    </v-app>
</template>

<script>
import Navigation from './Navigation';

export default {
    name: "Home",
    components: {
        Navigation,
    },
    data: () => ({

    }),
    props: {
        source: String
    }
};
</script>
