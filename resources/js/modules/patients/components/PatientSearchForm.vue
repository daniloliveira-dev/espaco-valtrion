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
.patient-filters { display: grid; grid-template-columns: minmax(180px, 1.1fr) minmax(180px, 1.1fr) minmax(170px, .9fr) auto auto; align-items: end; gap: 12px; margin-bottom: 18px; padding: 18px; overflow: visible; }
.patient-filter { display: grid; gap: 7px; color: #52617a; font-size: 11px; font-weight: 600; }
.patient-filter input { width: 100%; height: 42px; padding: 0 11px; border: 1px solid #dfe5ef; border-radius: 8px; outline: none; color: #293a57; background: #fff; font: inherit; font-weight: 400; }
.patient-filter input:focus { border-color: #397cdb; box-shadow: 0 0 0 3px rgb(57 124 219 / 12%); }
.filter-submit { width: auto; min-width: 94px; min-height: 42px; padding: 0 16px; font-size: 11px; }
.filter-clear { min-height: 42px; padding: 0 11px; border: 0; color: #6f7e95; background: transparent; font-size: 11px; font-weight: 700; }
.filter-clear:hover:not(:disabled) { color: #2857d2; }
.filter-clear:disabled { cursor: wait; opacity: .6; }

@media (max-width: 1050px) {
    .patient-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 560px) {
    .patient-filters { grid-template-columns: 1fr; gap: 13px; padding: 15px; }
    .filter-submit { width: 100%; }
    .filter-clear { min-height: 34px; }
}
</style>
