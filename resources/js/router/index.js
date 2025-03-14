import {createWebHashHistory, createRouter} from 'vue-router'

import HomeView from '../pages/index.vue'
import ManageView from '../pages/manage.vue'

const routes = [
    {path: '/', component: HomeView},
    {path: '/manage', component: ManageView},
]

const router = createRouter({
    history: createWebHashHistory(),
    routes,
})
export default router;
