<template>
    <div class="stat-grid">
        <article v-for="card in cards" :key="card.key" class="stat-card">
            <div class="stat-head">
                <span>{{ card.label }}</span>
                <span class="stat-icon" aria-hidden="true">
                    <svg v-if="card.key === 'total_patients'" viewBox="0 0 24 24" fill="none"><path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-5A4.5 4.5 0 0 0 2 18.5V20m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-1.5a4.5 4.5 0 0 0-3.5-4.38M16 2.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>
                    <svg v-else viewBox="0 0 24 24" fill="none"><path d="M7 3h7l5 5v11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M8 13h8m-8 4h8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>
                </span>
            </div>
            <div class="stat-value">{{ card.value }}</div>
            <div class="stat-foot">{{ card.detail }}</div>
        </article>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    metrics: { type: Object, required: true },
});

const cards = computed(() => [
    { key: 'total_patients', label: 'Pacientes cadastrados', value: props.metrics.total_patients, detail: 'Total de prontuários' },
    { key: 'total_exams', label: 'Exames cadastrados', value: props.metrics.total_exams, detail: 'Exames registrados no sistema' },
    { key: 'pending_exams', label: 'Exames pendentes', value: props.metrics.pending_exams, detail: 'Solicitados ou agendados' },
    { key: 'completed_this_month', label: 'Concluídos este mês', value: props.metrics.completed_this_month, detail: 'Exames com realização registrada' },
]);
</script>
