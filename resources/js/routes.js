import Home from './components/Home'
import Bill from './components/Bill'
import Account from './components/Account'
import Charts from './components/Charts'

export default [
    {
        path: '/',
        name: 'home',
        component: Home,
    },
    {
        path: '/bill',
        name: 'newBill',
        component: Bill,
    },
    {
        path: '/bill/:id',
        name: 'editBill',
        component: Bill,
        props: true
    },
    {
        path: '/account',
        name: 'account',
        component: Account,
    },
    {
        path: '/charts',
        name: 'charts',
        component: Charts
    }
];
