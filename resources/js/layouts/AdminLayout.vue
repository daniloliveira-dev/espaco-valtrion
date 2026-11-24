<template>
    <div class="dashboard-shell">
        <aside class="sidebar" :class="{ open: menuOpen }">
            <ValtrixLogo href="/dashboard" />

            <span class="nav-caption">Menu principal</span>
            <nav class="nav-list" aria-label="Navegação principal">
                <RouterLink class="nav-item" active-class="active" to="/dashboard">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" /></svg>
                    Visão geral
                </RouterLink>
                <RouterLink class="nav-item" active-class="active" to="/pacientes"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-5A4.5 4.5 0 0 0 2 18.5V20m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-1.5a4.5 4.5 0 0 0-3.5-4.38M16 2.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>Pacientes</RouterLink>
                <a class="nav-item" href="#" @click.prevent="showComingSoon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.7" /><path d="M16 3v4M8 3v4M3 10h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>Agenda</a>
                <a class="nav-item" href="#" @click.prevent="showComingSoon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14m-9 0v-5h4v5m-6-11h.01M12 10h.01M15 10h.01M9 13h.01M15 13h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>Unidades</a>
            </nav>

            <div class="sidebar-help">
                <strong>Precisa de ajuda?</strong>
                Nossa equipe está aqui para apoiar você.
            </div>
        </aside>

        <main class="main-area">
            <header class="topbar">
                <div class="page-location">
                    <button class="icon-button mobile-menu" type="button" aria-label="Abrir menu" @click="menuOpen = !menuOpen">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /></svg>
                    </button>
                    <span>Painel <span aria-hidden="true">/</span> <strong>{{ pageTitle }}</strong></span>
                </div>
                <div class="topbar-right">
                    <button class="icon-button" type="button" aria-label="Notificações">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 12h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <span class="notification-dot"></span>
                    </button>
                    <div ref="profileMenuElement" class="profile-menu">
                        <button
                            ref="profileTrigger"
                            class="profile-trigger"
                            type="button"
                            :aria-label="`Abrir menu de ${auth.user?.name ?? 'usuário'}`"
                            aria-controls="profile-menu"
                            :aria-expanded="profileMenuOpen"
                            @click="toggleProfileMenu"
                        >
                            {{ initials }}
                        </button>
                        <div v-if="profileMenuOpen" id="profile-menu" class="profile-dropdown">
                            <div class="profile-dropdown-user">
                                <strong>{{ auth.user?.name }}</strong>
                                <span>{{ auth.user?.email }}</span>
                            </div>
                            <button class="profile-menu-item" type="button" @click="goToProfile">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.7" /><path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>
                                Perfil
                            </button>
                            <button class="profile-menu-item profile-menu-logout" type="button" :disabled="loggingOut" @click="logout">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                {{ loggingOut ? 'Saindo...' : 'Sair' }}
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <RouterView />
        </main>
        <div v-if="notice" class="notice-toast" role="status">{{ notice }}</div>
        <div v-if="logoutError" class="logout-error" role="alert">{{ logoutError }}</div>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../modules/auth/stores/authStore';
import ValtrixLogo from '../shared/components/ValtrixLogo.vue';
import { getInitials } from '../shared/utils/initials';

const auth = useAuthStore();
const route = useRoute();
const router = useRouter();
const menuOpen = ref(false);
const profileMenuOpen = ref(false);
const profileMenuElement = ref(null);
const profileTrigger = ref(null);
const loggingOut = ref(false);
const notice = ref('');
const logoutError = ref('');
const pageTitle = computed(() => route.meta.title ?? 'Visão geral');

const initials = computed(() => getInitials(auth.user?.name));

function toggleProfileMenu() {
    profileMenuOpen.value = !profileMenuOpen.value;
}

function closeProfileMenu() {
    profileMenuOpen.value = false;
}

function handleOutsidePointer(event) {
    if (!profileMenuElement.value?.contains(event.target)) {
        closeProfileMenu();
    }
}

function handleMenuKeydown(event) {
    if (event.key === 'Escape' && profileMenuOpen.value) {
        closeProfileMenu();
        profileTrigger.value?.focus();
    }
}

async function goToProfile() {
    closeProfileMenu();
    await router.push({ name: 'profile' });
}

let noticeTimeout;
function showComingSoon() {
    notice.value = 'Este módulo estará disponível em breve.';
    clearTimeout(noticeTimeout);
    noticeTimeout = setTimeout(() => { notice.value = ''; }, 2800);
    menuOpen.value = false;
}

async function logout() {
    closeProfileMenu();
    loggingOut.value = true;
    logoutError.value = '';
    try {
        await auth.logout();
        await router.replace('/login');
    } catch {
        logoutError.value = 'Não foi possível encerrar a sessão. Tente novamente.';
    } finally {
        loggingOut.value = false;
    }
}

onMounted(() => {
    document.addEventListener('pointerdown', handleOutsidePointer);
    document.addEventListener('keydown', handleMenuKeydown);
});

onBeforeUnmount(() => {
    document.removeEventListener('pointerdown', handleOutsidePointer);
    document.removeEventListener('keydown', handleMenuKeydown);
    clearTimeout(noticeTimeout);
});
</script>

<style scoped>
.notice-toast, .logout-error { position: fixed; right: 24px; bottom: 24px; z-index: 5; max-width: min(390px, calc(100vw - 32px)); padding: 13px 17px; border-radius: 9px; color: #fff; background: #203d9b; box-shadow: 0 8px 26px rgb(26 57 132 / 18%); font-size: 12px; }
.logout-error { bottom: 76px; color: #8d2932; background: #fff1f1; }
</style>
