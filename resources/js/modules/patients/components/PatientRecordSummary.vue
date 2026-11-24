<template>
    <section class="record-summary" aria-label="Resumo do prontuário">
        <article v-for="item in summary" :key="item.label" class="summary-card">
            <span>{{ item.label }}</span>
            <strong>{{ item.value }}</strong>
        </article>
    </section>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    consultations: { type: Array, required: true },
});

const summary = computed(() => {
    const exams = props.consultations.flatMap((consultation) => consultation.exams ?? []);

    return [
        { label: 'Consultas', value: props.consultations.length },
        { label: 'Exames realizados', value: exams.filter((exam) => exam.status === 'completed').length },
        { label: 'Resultados disponíveis', value: exams.filter((exam) => Boolean(exam.result_summary?.trim())).length },
        { label: 'Exames pendentes', value: exams.filter((exam) => ['requested', 'scheduled'].includes(exam.status)).length },
    ];
});
</script>

<style scoped>
.record-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 18px; }
.summary-card { display: grid; gap: 8px; padding: 15px 17px; border: 1px solid #e9edf4; border-radius: 11px; background: #fff; }
.summary-card span { color: #7e8ba0; font-size: 10px; font-weight: 600; }
.summary-card strong { color: #293a57; font-size: 21px; line-height: 1; }

@media (max-width: 720px) {
    .record-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 400px) {
    .summary-card { padding: 13px; }
    .summary-card strong { font-size: 18px; }
}
</style>
