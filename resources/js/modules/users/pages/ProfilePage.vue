<template>
    <section class="dashboard-content profile-page">
        <div class="welcome-line">
            <div>
                <h1>Meu perfil</h1>
                <p>Consulte as informações da sua conta Valtrix.</p>
            </div>
        </div>

        <article class="panel profile-card">
            <div class="profile-summary">
                <div class="profile-large-avatar" aria-hidden="true">{{ initials }}</div>
                <div class="profile-summary-copy">
                    <span class="profile-status"><i></i> Conta ativa</span>
                    <h2>{{ auth.user?.name }}</h2>
                    <p>{{ auth.user?.email }}</p>
                </div>
            </div>

            <div class="profile-details">
                <div class="profile-detail">
                    <span>Nome completo</span>
                    <strong>{{ auth.user?.name }}</strong>
                </div>
                <div class="profile-detail">
                    <span>E-mail de acesso</span>
                    <strong>{{ auth.user?.email }}</strong>
                </div>
            </div>
        </article>
    </section>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../../auth/stores/authStore';
import { getInitials } from '../../../shared/utils/initials';

const auth = useAuthStore();

const initials = computed(() => getInitials(auth.user?.name));
</script>

<style scoped>
.profile-card { padding: 28px; }
.profile-summary { display: flex; align-items: center; gap: 18px; padding-bottom: 25px; border-bottom: 1px solid #edf0f5; }
.profile-large-avatar { width: 68px; height: 68px; display: grid; place-items: center; border-radius: 22px; color: #fff; background: var(--brand-gradient); box-shadow: 0 8px 18px rgb(40 87 210 / 16%); font-size: 19px; font-weight: 700; }
.profile-summary-copy h2 { margin: 8px 0 4px; color: #17243b; font-size: 20px; letter-spacing: -.4px; }
.profile-summary-copy p { margin: 0; color: #758198; font-size: 13px; }
.profile-status { display: inline-flex; align-items: center; gap: 6px; color: #328d73; font-size: 10px; font-weight: 700; }
.profile-status i { width: 6px; height: 6px; border-radius: 50%; background: #45b58e; }
.profile-details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 22px; padding-top: 24px; }
.profile-detail { display: grid; gap: 7px; }
.profile-detail span { color: #8a96aa; font-size: 11px; }
.profile-detail strong { overflow-wrap: anywhere; color: #33425d; font-size: 13px; font-weight: 600; }

@media (max-width: 560px) {
    .profile-card { padding: 20px; }
    .profile-details { grid-template-columns: 1fr; gap: 18px; }
}
</style>
