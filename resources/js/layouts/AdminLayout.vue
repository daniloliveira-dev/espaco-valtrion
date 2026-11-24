<template>
    <div class="dashboard-shell">
        <aside class="sidebar" :class="{ open: menuOpen }">
            <ValtrixLogo href="/dashboard" />

            <span class="nav-caption">Menu principal</span>
            <nav class="nav-list" aria-label="Navegação principal">
                <RouterLink class="nav-item active" to="/dashboard">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" /></svg>
                    Visão geral
                </RouterLink>
                <a class="nav-item" href="#" @click.prevent="showComingSoon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-5A4.5 4.5 0 0 0 2 18.5V20m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-1.5a4.5 4.5 0 0 0-3.5-4.38M16 2.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>Pacientes</a>
                <a class="nav-item" href="#" @click.prevent="showComingSoon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.7" /><path d="M16 3v4M8 3v4M3 10h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>Agenda</a>
                <a class="nav-item" href="#" @click.prevent="showComingSoon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14m-9 0v-5h4v5m-6-11h.01M12 10h.01M15 10h.01M9 13h.01M15 13h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>Unidades</a>
            </nav>

            <div class="sidebar-help">
                <strong>Precisa de ajuda?</strong>
                Nossa equipe está aqui para apoiar você.
            </div>
            <div class="sidebar-bottom">
                <button class="logout-button" type="button" :disabled="loggingOut" @click="logout">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" aria-hidden="true"><path d="M10 17l5-5-5-5m5 5H3m9-9h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>
                    {{ loggingOut ? 'Saindo...' : 'Sair da conta' }}
                </button>
            </div>
        </aside>

        <main class="main-area">
            <header class="topbar">
                <div class="page-location">
                    <button class="icon-button mobile-menu" type="button" aria-label="Abrir menu" @click="menuOpen = !menuOpen">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /></svg>
                    </button>
                    <span>Painel <span aria-hidden="true">/</span> <strong>Visão geral</strong></span>
                </div>
                <div class="topbar-right">
                    <button class="icon-button" type="button" aria-label="Notificações">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 12h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        <span class="notification-dot"></span>
                    </button>
                    <div class="profile-chip">
                        <span class="profile-avatar">{{ initials }}</span>
                        <span class="profile-copy">
                            <span class="profile-name">{{ auth.user?.name }}</span>
                            <span class="profile-role">Equipe de gestão</span>
                        </span>
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
import { computed, ref } from 'vue';
import { RouterLink, RouterView, useRouter } from 'vue-router';
import { useAuthStore } from '../modules/auth/stores/authStore';
import ValtrixLogo from '../shared/components/ValtrixLogo.vue';

const auth = useAuthStore();
const router = useRouter();
const menuOpen = ref(false);
const loggingOut = ref(false);
const notice = ref('');
const logoutError = ref('');

const initials = computed(() => auth.user?.name
    ?.split(/\s+/)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join('') ?? '');

let noticeTimeout;
function showComingSoon() {
    notice.value = 'Este módulo estará disponível em breve.';
    clearTimeout(noticeTimeout);
    noticeTimeout = setTimeout(() => { notice.value = ''; }, 2800);
    menuOpen.value = false;
}

async function logout() {
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
</script>

<style scoped>
.notice-toast, .logout-error { position: fixed; right: 24px; bottom: 24px; z-index: 5; max-width: min(390px, calc(100vw - 32px)); padding: 13px 17px; border-radius: 9px; color: #fff; background: #203d9b; box-shadow: 0 8px 26px rgb(26 57 132 / 18%); font-size: 12px; }
.logout-error { bottom: 76px; color: #8d2932; background: #fff1f1; }
</style>
