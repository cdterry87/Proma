<template>
    <div>
        <EditTeam v-if="editTeam" :teamInfo="team" />
        <ViewTeam v-else :teamInfo="team" />
        <TeamMembers :teamInfo="team" :teamMembers="members" />
    </div>
</template>

<script>
    import EventBus from './../eventbus'
    import EditTeam from './EditTeam'
    import ViewTeam from './ViewTeam'
    import TeamMembers from './TeamMembers'

    export default {
        name: 'Team',
        props: ['id'],
        components: {
            EditTeam,
            ViewTeam,
            TeamMembers
        },
        data() {
            return {
                editTeam: false,
                team: '',
                members: ''
            }
        },
        methods: {
            getTeam() {
                axios.get('/api/teams/' + this.id)
                .then(response => {
                    this.team = response.data

                    this.getTeamMembers(this.id);
                })
            },
            getTeamMembers(team_id) {
                axios.get('/api/members/' + team_id)
                .then(response => {
                    this.members = response.data
                })
            },
        },
        created() {
            EventBus.$on('editTeam', editTeam => {
                this.editTeam = editTeam
            })

            EventBus.$on('addMember', () => {
                this.getTeamMembers(this.id)
            })

            EventBus.$on('loadMembers', team_id => {
                this.getTeamMembers(team_id)
            })
        },
        mounted() {
            this.getTeam()
        }
    }
</script>

<style scoped>

</style>
