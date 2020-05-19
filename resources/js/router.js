import Vue from 'vue'
import VueRouter from 'vue-router'
import Admin from './components/AdminDashboard'
import Login from './components/Login'
import RolesComponent from './components/RolesComponent'


import vueti from './components/Vueti'

Vue.use(VueRouter)

const  routes = [
    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/admin',
        component: Admin,
        name: 'Admin',
        children : [
            {
                path: 'roles',
                component : RolesComponent,
                name : "Roles"
            }
        ],
        beforeEnter: (to, from, next) => {
            axios.get('/api/verify')
            .then(res => {
                
                return next()
            })
            .catch(err => {
                return next("/login")
            })
        }

        
    },
    {
        path: '/login',
        component: Login,
        name: 'Login',
        beforeEnter: (to, from, next) => {
            if(localStorage.getItem('token')){
                axios.get('/api/verify')
                .then(res => {
                    return next("/admin")
                })
                .catch(err => {
                    return next()
                })
            }
            return next()
            
        }
        
    },
    {
        path: '/vuetify',
        component: vueti,
        name: 'Vueti'
    }
]


const router = new VueRouter({routes})

router.beforeEach((to, from, next) => {

    const token = localStorage.getItem('token') || null
    window.axios.defaults.headers['Authorization'] = `Bearer ${token}`;
    return next()
  })

export default router