<template>
    <div>
        <EditTeam v-if="editTeam" :teamInfo="team" />
        <ViewTeam v-else :teamInfo="team" />
    </div>
</template>

<script>
    import eventBus from './../events';
    import EditTeam from './EditTeam'
    import ViewTeam from './ViewTeam'

    export default {
        name: 'Team',
        props: ['id'],
        components: {
            EditTeam,
            ViewTeam
        },
        data() {
            return {
                editTeam: false,
                team: ''
            }
        },
        methods: {
            getUserData() {
                this.userData = JSON.parse(localStorage.getItem('userData'))
            },
            getTeam() {
                axios.get('/api/teams/' + this.id)
                .then(response => {
                    this.team = response.data
                })
            },
        },
        created() {
            this.getUserData()

            eventBus.$on('editTeam', editTeam => {
                this.editTeam = editTeam
            })
        },
        mounted() {
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt

            this.getTeam()
        }
    }
</script>

<style scoped>

</style>
