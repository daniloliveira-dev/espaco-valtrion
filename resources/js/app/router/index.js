import { createRouter, createWebHistory } from 'vue-router';
import AuthLayout from '../../layouts/AuthLayout.vue';
import AdminLayout from '../../layouts/AdminLayout.vue';
import LoginPage from '../../modules/auth/pages/LoginPage.vue';
import RegisterPage from '../../modules/auth/pages/RegisterPage.vue';
import DashboardPage from '../../modules/dashboard/pages/DashboardPage.vue';
import ServiceUnavailablePage from '../../shared/pages/ServiceUnavailablePage.vue';
import { useAuthStore } from '../../modules/auth/stores/authStore';

const routes = [
    {
        path: '/',
        redirect: '/dashboard',
    },
    {
        path: '/',
        component: AuthLayout,
        children: [
            { path: 'login', name: 'login', component: LoginPage, meta: { guestOnly: true } },
            { path: 'register', name: 'register', component: RegisterPage, meta: { guestOnly: true } },
        ],
    },
    {
        path: '/dashboard',
        component: AdminLayout,
        children: [
            { path: '', name: 'dashboard', component: DashboardPage, meta: { requiresAuth: true } },
        ],
    },
    {
        path: '/indisponivel',
        name: 'service-unavailable',
        component: ServiceUnavailablePage,
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/dashboard',
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    if (!auth.initialized) {
        try {
            await auth.bootstrap();
        } catch (error) {
            console.error('Unable to restore the current session.', error);
            return { name: 'service-unavailable' };
        }
    }

    if (to.meta.requiresAuth && !auth.user) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.meta.guestOnly && auth.user) {
        return { name: 'dashboard' };
    }
});

export default router;
