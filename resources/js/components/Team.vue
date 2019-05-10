<template>
    <div>
        <EditTeam v-if="editTeam" :teamInfo="team" />
        <ViewTeam v-else :teamInfo="team" />
    </div>
</template>

<script>
    import EventBus from './../eventbus';
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
            getTeam() {
                axios.get('/api/teams/' + this.id)
                .then(response => {
                    this.team = response.data
                })
            },
        },
        created() {
            EventBus.$on('editTeam', editTeam => {
                this.editTeam = editTeam
            })
        },
        mounted() {
            this.getTeam()
        }
    }
</script>

<style scoped>

</style>
