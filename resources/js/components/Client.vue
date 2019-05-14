<template>
    <div>
        <EditClient v-if="editClient" :clientInfo="client" />
        <ViewClient v-else :clientInfo="client" />
        <ClientContacts :clientInfo="client" :clientContacts="contacts" />
    </div>
</template>

<script>
    import EventBus from './../eventbus'
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
            EventBus.$on('editClient', editClient => {
                this.editClient = editClient
            })

            EventBus.$on('createContact', contacts => {
                this.contacts = contacts
            })

            EventBus.$on('loadContacts', client_id => {
                this.getClientContacts(client_id)
            })
        },
        mounted() {
            this.getClient()
        }
    }
</script>

<style scoped>

</style>
