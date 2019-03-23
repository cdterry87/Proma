<template>
    <div>
        <EditTeam v-if="editTeam" :team="team" />
        <ViewTeam v-else :team="team" />
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
            updateTeam() {
                let name = this.team.name;
                let description = this.team.description;

                axios.put('/api/teams/' + this.id, { name, description })
                .then(response => {
                    this.team = response.data.data
                })
            },
        },
        created() {
            this.getUserData()

            eventBus.$on('editTeam', editTeam => {
                console.log('editTeam', editTeam)
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
