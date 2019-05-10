<template>
    <div>
        <EditClient v-if="editClient" :clientInfo="client" />
        <ViewClient v-else :clientInfo="client" />
        <ClientContacts :clientInfo="client" :clientContacts="contacts" />
    </div>
</template>

<script>
    import EventBus from './../eventbus';
    import EditClient from './EditClient'
    import ViewClient from './ViewClient'
    import ClientContacts from './ClientContacts'

    export default {
        name: 'Client',
        props: ['id'],
        components: {
            EditClient,
            ViewClient,
            ClientContacts
        },
        data() {
            return {
                editClient: false,
                client: '',
                contacts: ''
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

                    this.getClientContacts(this.id);
                })
            },
            getClientContacts(client_id) {
                axios.get('/api/contacts/' + client_id)
                .then(response => {
                    this.contacts = response.data
                })
            },
        },
        created() {
            this.getUserData()

            eventBus.$on('editClient', editClient => {
                this.editClient = editClient
            })

            eventBus.$on('createContact', contacts => {
                this.contacts = contacts
            })

            eventBus.$on('loadContacts', client_id => {
                this.getClientContacts(client_id)
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
