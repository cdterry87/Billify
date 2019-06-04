import Bills from './components/Bills'
import Bill from './components/Bill'

export default [
    {
        path: '/',
        name: 'home',
        component: Bills,
    },
    {
        path: '/bills/:id',
        name: 'bill',
        component: Bill,
        props: true
    },
];
