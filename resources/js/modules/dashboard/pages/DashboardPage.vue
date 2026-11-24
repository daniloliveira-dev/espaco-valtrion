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
.dashboard-loading { min-height: 260px; display: flex; align-items: center; justify-content: center; gap: 11px; color: #68778e; font-size: 12px; }
.dashboard-error { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 15px; border: 1px solid #f1d4d5; border-radius: 9px; color: #9c3d44; background: #fff7f7; font-size: 12px; }
.dashboard-error button { border: 0; color: #2857d2; background: transparent; font-size: 11px; font-weight: 700; white-space: nowrap; }
</style>
