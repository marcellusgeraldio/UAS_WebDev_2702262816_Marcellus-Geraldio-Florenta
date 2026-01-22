import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue' 

const routes = [
    {
        path: '/',
        name: 'login',
        component: Login
    },
    {
        path: '/mahasiswa',
        name: 'mahasiswa.index',
        component: () => import('../views/mahasiswa/Index.vue')
    },
    {
        path: '/mahasiswa/create',
        name: 'mahasiswa.create',
        component: () => import('../views/mahasiswa/Create.vue')
    },
    {
        path: '/mahasiswa/edit/:id', 
        name: 'mahasiswa.edit',
        component: () => import('../views/mahasiswa/Edit.vue')
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router