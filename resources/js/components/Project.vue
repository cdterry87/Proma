<template>
    <div>
        <EditProject v-if="editProject" :projectInfo="project" />
        <ViewProject v-else :projectInfo="project" />
        <ProjectTasks :projectInfo="project" />
    </div>
</template>

<script>
    import eventBus from './../events';
    import EditProject from './EditProject'
    import ViewProject from './ViewProject'
    import ProjectTasks from './ProjectTasks'

    export default {
        name: 'Project',
        props: ['id'],
        components: {
            EditProject,
            ViewProject,
            ProjectTasks
        },
        data() {
            return {
                editProject: false,
                project: ''
            }
        },
        methods: {
            getUserData() {
                this.userData = JSON.parse(localStorage.getItem('userData'))
            },
            getProject() {
                axios.get('/api/projects/' + this.id)
                .then(response => {
                    this.project = response.data
                })
            },
        },
        created() {
            this.getUserData()

            eventBus.$on('editProject', editProject => {
                this.editProject = editProject
            })
        },
        mounted() {
            axios.defaults.headers.common['Content-Type'] = 'application/json'
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.userData.jwt

            this.getProject()
        }
    }
</script>

<style scoped>

</style>
