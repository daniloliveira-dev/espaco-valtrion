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
.patient-error { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 14px 16px; border: 1px solid #f1d4d5; border-radius: 9px; color: #9c3d44; background: #fff7f7; font-size: 12px; }
.patient-error button { border: 0; color: #2857d2; background: transparent; font-size: 11px; font-weight: 700; white-space: nowrap; }
</style>
