<template>
    <div class="stat-grid">
        <article
            v-for="(card, index) in cards"
            :key="card.key"
            class="stat-card"
            :style="{ animationDelay: `${index * 70}ms` }"
        >
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

<style scoped>
.stat-grid { margin-bottom: 24px; }

.stat-card {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    border-color: #e3eaf3;
    border-radius: 15px;
    box-shadow: 0 3px 12px rgb(34 55 91 / 4%);
    animation: card-enter 520ms cubic-bezier(.2, .75, .25, 1) backwards;
    transition: transform 220ms ease, border-color 220ms ease, box-shadow 220ms ease;
}

.stat-card::before {
    position: absolute;
    z-index: -1;
    inset: 0 0 auto;
    height: 3px;
    background: var(--card-accent, #4381e7);
    content: '';
}

.stat-card::after {
    position: absolute;
    z-index: -1;
    top: -54px;
    right: -44px;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: var(--card-glow, rgb(67 129 231 / 6%));
    content: '';
    transition: transform 300ms ease;
}

.stat-card:nth-child(1) { --card-accent: #38b99a; --card-glow: rgb(56 185 154 / 8%); }
.stat-card:nth-child(2) { --card-accent: #5685e8; --card-glow: rgb(86 133 232 / 8%); }
.stat-card:nth-child(3) { --card-accent: #e4a943; --card-glow: rgb(228 169 67 / 10%); }
.stat-card:nth-child(4) { --card-accent: #8a70d6; --card-glow: rgb(138 112 214 / 9%); }

.stat-card:hover {
    transform: translateY(-4px);
    border-color: #d3deef;
    box-shadow: 0 12px 26px rgb(34 55 91 / 10%);
}

.stat-card:hover::after { transform: scale(1.2); }
.stat-head { position: relative; z-index: 1; gap: 12px; font-size: 14px; font-weight: 600; }
.stat-icon { width: 38px; height: 38px; flex: none; border-radius: 11px; }
.stat-icon svg { width: 19px; height: 19px; }
.stat-value { position: relative; z-index: 1; margin-top: 16px; font-size: 30px; font-variant-numeric: tabular-nums; }
.stat-foot { position: relative; z-index: 1; margin-top: 6px; font-size: 13px; }

@keyframes card-enter {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

:global(:root[data-theme='dark']) .stat-card {
    border-color: #2a374b;
    box-shadow: 0 3px 14px rgb(0 0 0 / 10%);
}

:global(:root[data-theme='dark']) .stat-card:hover {
    border-color: #40516d;
    box-shadow: 0 12px 28px rgb(0 0 0 / 24%);
}

@media (prefers-reduced-motion: reduce) {
    .stat-card { animation: none; transition: none; }
    .stat-card::after { transition: none; }
}
</style>
