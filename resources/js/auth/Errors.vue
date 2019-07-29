<template>
    <div>
        <v-alert :value="show" type="error">
            <ul>
                <li v-for="(message, index) in errorMessages" :key="index">{{ message }}</li>
            </ul>
        </v-alert>
    </div>
</template>

<script>
    export default {
        name: 'Errors',
        props: ['errors'],
        computed: {
            show: function() {
                if (this.errors.length > 0) {
                    return true
                }
                return false
            },
            errorMessages: function() {
                return JSON.parse(this.errors)
            }
        },
    }
</script>
