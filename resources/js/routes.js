import Teams from './components/Teams'
import Team from './components/Team'
import Clients from './components/Clients'
import Client from './components/Client'
import Projects from './components/Projects'
import Project from './components/Project'

export default [
    {
        path: '/',
        name: 'projects',
        component: Projects,
    },
    {
        path: '/teams',
        name: 'teams',
        component: Teams,
    },
    {
        path: '/team/:id',
        name: 'team',
        component: Team,
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
    {
        path: '/project/:id',
        name: 'project',
        component: Project,
        props: true
    }
];
