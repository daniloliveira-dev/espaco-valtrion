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
.record-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 13px; margin-bottom: 20px; }
.summary-card { position: relative; overflow: hidden; display: grid; gap: 9px; padding: 17px 18px; border: 1px solid #e5ebf4; border-radius: 13px; background: #fff; box-shadow: 0 4px 15px rgb(34 55 91 / 4%); animation: summary-enter 430ms cubic-bezier(.2, .75, .25, 1) backwards; transition: transform 180ms ease, border-color 180ms ease, box-shadow 180ms ease; }
.summary-card:nth-child(2) { animation-delay: 45ms; }
.summary-card:nth-child(3) { animation-delay: 90ms; }
.summary-card:nth-child(4) { animation-delay: 135ms; }
.summary-card::before { position: absolute; inset: 0 auto 0 0; width: 3px; background: linear-gradient(180deg, #40d5a5, #397cdb); content: ''; }
.summary-card:hover { transform: translateY(-3px); border-color: #d3deef; box-shadow: 0 9px 22px rgb(34 55 91 / 9%); }
.summary-card span { color: #7e8ba0; font-size: 14px; font-weight: 600; }
.summary-card strong { color: #293a57; font-size: 25px; line-height: 1; font-variant-numeric: tabular-nums; }

@keyframes summary-enter {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

:global(:root[data-theme='dark']) .summary-card {
    border-color: #2a374b;
    box-shadow: 0 4px 15px rgb(0 0 0 / 14%);
}

:global(:root[data-theme='dark']) .summary-card:hover {
    border-color: #40516d;
    box-shadow: 0 9px 22px rgb(0 0 0 / 24%);
}

@media (max-width: 720px) {
    .record-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 400px) {
    .summary-card { padding: 14px; }
    .summary-card strong { font-size: 22px; }
}

@media (prefers-reduced-motion: reduce) {
    .summary-card { animation: none; transition: none; }
}
</style>
