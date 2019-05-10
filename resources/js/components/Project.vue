<template>
    <div>
        <EditProject v-if="editProject" :projectInfo="project" />
        <ViewProject v-else :projectInfo="project" />
        <ProjectTasks :projectInfo="project" :projectTasks="tasks" />
    </div>
</template>

<script>
    import EventBus from './../eventbus';
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
                project: '',
                tasks: '',
            }
        },
        methods: {
            getProject() {
                axios.get('/api/projects/' + this.id)
                .then(response => {
                    this.project = response.data

                    this.getProjectTasks(this.id);
                })
            },
            getProjectTasks(project_id) {
                axios.get('/api/tasks/' + project_id)
                .then(response => {
                    this.tasks = response.data
                })
            },
        },
        created() {
            EventBus.$on('editProject', editProject => {
                this.editProject = editProject
            })

            EventBus.$on('createTask', tasks => {
                this.tasks = tasks
            })

            EventBus.$on('loadTasks', project_id => {
                this.getProjectTasks(project_id)
            })
        },
        mounted() {
            this.getProject()
        }
    }
</script>

<style scoped>

</style>
