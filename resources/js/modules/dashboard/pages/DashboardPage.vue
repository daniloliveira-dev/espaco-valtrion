<template>
    <section class="dashboard-content">
        <div class="welcome-line">
            <div>
                <h1>Visão geral de pacientes</h1>
                <p>Acompanhe os prontuários e o andamento dos exames cadastrados.</p>
            </div>
        </div>

        <div v-if="error" class="dashboard-error" role="alert">
            <span>{{ error }}</span>
            <button type="button" @click="loadDashboard">Tentar novamente</button>
        </div>
        <div v-else-if="loading" class="dashboard-loading" aria-label="Carregando painel">
            <span class="spinner" aria-hidden="true"></span>
            Carregando dados de pacientes e exames...
        </div>
        <template v-else-if="dashboard">
            <PatientOverviewCards :metrics="dashboard.metrics" />
            <RecentExamsList :exams="dashboard.recent_exams" />
        </template>
    </section>
</template>

<script setup>
import { onMounted } from 'vue';
import PatientOverviewCards from '../components/PatientOverviewCards.vue';
import RecentExamsList from '../components/RecentExamsList.vue';
import { useDashboardOverview } from '../composables/useDashboardOverview';

const { dashboard, loading, error, loadDashboard } = useDashboardOverview();

onMounted(loadDashboard);
</script>

<style scoped>
.welcome-line { animation: welcome-enter 480ms cubic-bezier(.2, .75, .25, 1) both; }
.welcome-line h1 { font-size: 29px; letter-spacing: -1.1px; }
.welcome-line p { line-height: 1.65; }
.dashboard-loading { min-height: 260px; display: flex; align-items: center; justify-content: center; gap: 11px; color: #68778e; font-size: 14px; }
.dashboard-error { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 15px; border: 1px solid #f1d4d5; border-radius: 9px; color: #9c3d44; background: #fff7f7; font-size: 14px; }
.dashboard-error button { border: 0; color: #2857d2; background: transparent; font-size: 13px; font-weight: 700; white-space: nowrap; }

@keyframes welcome-enter {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 760px) {
    .welcome-line h1 { font-size: 25px; }
}

@media (prefers-reduced-motion: reduce) {
    .welcome-line { animation: none; }
}
</style>
