import Projects from './components/Projects'
import Project from './components/Project'
import Issues from './components/Issues'
import Issue from './components/Issue'
import Clients from './components/Clients'
import Client from './components/Client'

export default [
    {
        path: '/',
        name: 'projects',
        component: Projects,
    },
    {
        path: '/project/:id',
        name: 'project',
        component: Project,
        props: true
    },
    {
        path: '/issues',
        name: 'issues',
        component: Issues,
    },
    {
        path: '/issue/:id',
        name: 'issue',
        component: Issue,
        props: true
    },
    {
        path: '/clients',
        name: 'clients',
        component: Clients,
    },
    {
        path: '/client/:id',
        name: 'client',
        component: Client,
        props: true
    },
];
