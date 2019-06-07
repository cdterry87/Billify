import Home from './components/Home'
import BillForm from './components/BillForm'
import Bill from './components/Bill'

export default [
    {
        path: '/',
        name: 'home',
        component: Home,
    },
    {
        path: '/bill',
        name: 'billForm',
        component: BillForm,
    },
    {
        path: '/bill/:id',
        name: 'bill',
        component: Bill,
        props: true
    },
];
