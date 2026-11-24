<template>
    <section class="dashboard-content patients-page">
        <div class="welcome-line">
            <div>
                <h1>Pacientes</h1>
                <p>Encontre pacientes e consulte o histórico de atendimentos e exames.</p>
            </div>
        </div>

        <PatientSearchForm
            v-model:name="filters.name"
            v-model:exam="filters.exam"
            v-model:date="filters.date"
            :loading="loading"
            @submit="applyFilters"
            @clear="clearFilters"
        />

        <div v-if="error" class="patient-error" role="alert">
            <span>{{ error }}</span>
            <button type="button" @click="loadPatients(currentPage)">Tentar novamente</button>
        </div>
        <PatientResults
            v-else
            :result="result"
            :loading="loading"
            :total-label="totalLabel"
            @page-change="loadPatients"
        />
    </section>
</template>

<script setup>
import { onMounted } from 'vue';
import PatientResults from '../components/PatientResults.vue';
import PatientSearchForm from '../components/PatientSearchForm.vue';
import { usePatientSearch } from '../composables/usePatientSearch';

defineOptions({ name: 'PatientsPage' });

const {
    filters,
    result,
    loading,
    error,
    currentPage,
    totalLabel,
    loadPatients,
    applyFilters,
    clearFilters,
} = usePatientSearch();

onMounted(() => loadPatients());
</script>

<style scoped>
.patients-page { max-width: 1440px; }
.patients-page > .welcome-line { animation: patients-enter 440ms cubic-bezier(.2, .75, .25, 1) both; }
.patient-error { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 15px 17px; border: 1px solid #f1d4d5; border-radius: 12px; color: #9c3d44; background: #fff7f7; box-shadow: 0 5px 16px rgb(130 48 57 / 5%); font-size: 14px; animation: patients-enter 360ms ease both; }
.patient-error button { border: 0; border-radius: 7px; padding: 8px 10px; color: #2857d2; background: transparent; font-size: 13px; font-weight: 700; white-space: nowrap; transition: background-color 160ms ease, color 160ms ease; }
.patient-error button:hover { color: #2148e8; background: #edf2ff; }

@keyframes patients-enter {
    from { opacity: 0; transform: translateY(9px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (prefers-reduced-motion: reduce) {
    .patients-page > .welcome-line,
    .patient-error { animation: none; }
    .patient-error button { transition: none; }
}
</style>
