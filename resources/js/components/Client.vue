<template>
    <div>
        <EditClient v-if="editClient" :clientInfo="client" />
        <ViewClient v-else :clientInfo="client" />
    </div>
</template>

<script>
    import eventBus from './../events';
    import EditClient from './EditClient'
    import ViewClient from './ViewClient'

    export default {
        name: 'Client',
        props: ['id'],
        components: {
            EditClient,
            ViewClient
        },
        data() {
            return {
                editClient: false,
                client: ''
            }
        },
        methods: {
            getUserData() {
                this.userData = JSON.parse(localStorage.getItem('userData'))
            },
            getClient() {
                axios.get('/api/clients/' + this.id)
                .then(response => {
                    this.client = response.data
                })
            },
        },
        created() {
            this.getUserData()

            eventBus.$on('editClient', editClient => {
                this.editClient = editClient
            })
        },
        mounted() {
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt

            this.getClient()
        }
    }
</script>

<style scoped>

</style>
