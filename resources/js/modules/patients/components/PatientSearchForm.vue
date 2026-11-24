<template>
    <form class="panel patient-filters" aria-label="Filtros de pacientes" @submit.prevent="emit('submit')">
        <label class="patient-filter">
            <span>Nome do paciente</span>
            <input :value="name" type="search" placeholder="Buscar por nome" autocomplete="off" @input="emit('update:name', $event.target.value)">
        </label>
        <label class="patient-filter">
            <span>Exame</span>
            <input :value="exam" type="search" placeholder="Ex.: Hemograma" autocomplete="off" @input="emit('update:exam', $event.target.value)">
        </label>
        <label class="patient-filter">
            <span>Data da consulta</span>
            <input :value="date" type="date" @input="emit('update:date', $event.target.value)">
        </label>
        <button class="primary-button filter-submit" type="submit" :disabled="loading">
            {{ loading ? 'Buscando...' : 'Buscar' }}
        </button>
        <button class="filter-clear" type="button" :disabled="loading" @click="emit('clear')">Limpar</button>
    </form>
</template>

<script setup>
defineProps({
    name: { type: String, default: '' },
    exam: { type: String, default: '' },
    date: { type: String, default: '' },
    loading: { type: Boolean, default: false },
});
const emit = defineEmits(['update:name', 'update:exam', 'update:date', 'submit', 'clear']);
</script>

<style scoped>
.patient-filters { display: grid; grid-template-columns: minmax(180px, 1.1fr) minmax(180px, 1.1fr) minmax(170px, .9fr) auto auto; align-items: end; gap: 14px; margin-bottom: 22px; padding: 21px; overflow: visible; border-radius: 15px; box-shadow: 0 5px 20px rgb(34 55 91 / 5%); animation: filter-panel-enter 500ms 60ms cubic-bezier(.2, .75, .25, 1) both; }
.patient-filter { display: grid; gap: 8px; color: #52617a; font-size: 14px; font-weight: 600; }
.patient-filter input { width: 100%; height: 46px; padding: 0 13px; border: 1px solid #dfe5ef; border-radius: 10px; outline: none; color: #293a57; background: #fff; font: inherit; font-weight: 400; transition: border-color 170ms ease, box-shadow 170ms ease, background-color 170ms ease; }
.patient-filter input:focus { border-color: #397cdb; box-shadow: 0 0 0 4px rgb(57 124 219 / 14%); }
.filter-submit { width: auto; min-width: 100px; min-height: 46px; padding: 0 17px; border-radius: 10px; font-size: 14px; transition: transform 170ms ease, box-shadow 170ms ease, filter 170ms ease; }
.filter-clear { min-height: 42px; padding: 0 12px; border: 0; border-radius: 8px; color: #6f7e95; background: transparent; font-size: 14px; font-weight: 700; transition: color 160ms ease, background-color 160ms ease; }
.filter-clear:hover:not(:disabled) { color: #2857d2; background: #f0f4ff; }
.filter-clear:disabled { cursor: wait; opacity: .6; }

@keyframes filter-panel-enter {
    from { opacity: 0; transform: translateY(11px); }
    to { opacity: 1; transform: translateY(0); }
}

:global(:root[data-theme='dark']) .patient-filters {
    border-color: #2a374b;
    box-shadow: 0 5px 20px rgb(0 0 0 / 14%);
}

:global(:root[data-theme='dark']) .patient-filter input:focus {
    border-color: #7899f1;
    box-shadow: 0 0 0 4px rgb(120 153 241 / 17%);
}

:global(:root[data-theme='dark']) .filter-clear:hover:not(:disabled) {
    color: #b7caff;
    background: #202f4c;
}

@media (max-width: 1050px) {
    .patient-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 560px) {
    .patient-filters { grid-template-columns: 1fr; gap: 13px; padding: 15px; }
    .filter-submit { width: 100%; }
    .filter-clear { min-height: 34px; }
}

@media (prefers-reduced-motion: reduce) {
    .patient-filters { animation: none; }
    .patient-filter input,
    .filter-submit,
    .filter-clear { transition: none; }
}
</style>
