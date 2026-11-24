<template>
    <section class="panel patient-results">
        <header class="patient-results-header">
            <div>
                <h2>Lista de pacientes</h2>
                <p>{{ totalLabel }}</p>
            </div>
            <span class="results-count">{{ result.meta?.total ?? 0 }} registros</span>
        </header>

        <div v-if="loading && !result.data.length" class="patient-loading">
            <span class="spinner" aria-hidden="true"></span>
            Carregando pacientes...
        </div>
        <div v-else-if="!result.data.length" class="patient-empty">
            <span class="empty-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-5A4.5 4.5 0 0 0 2 18.5V20m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-1.5a4.5 4.5 0 0 0-3.5-4.38M16 2.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" /></svg>
            </span>
            <strong>Nenhum paciente encontrado</strong>
            <span>Experimente alterar os filtros ou limpar a busca.</span>
        </div>
        <div v-else class="patient-table-scroll">
            <table class="patient-table">
                <thead>
                    <tr>
                        <th scope="col">Paciente</th>
                        <th scope="col">Prontuário</th>
                        <th scope="col">Última consulta</th>
                        <th scope="col">Consultas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="patient in result.data" :key="patient.id">
                        <td>
                            <RouterLink class="patient-name" :to="{ name: 'patient-details', params: { id: patient.id } }">
                                <span class="patient-list-avatar">{{ getInitials(patient.name) }}</span>
                                <span>
                                    <strong>{{ patient.name }}</strong>
                                    <small>{{ patient.email || 'E-mail não informado' }}</small>
                                </span>
                            </RouterLink>
                        </td>
                        <td><span class="record-number">{{ patient.medical_record_number }}</span></td>
                        <td>
                            <span v-if="patient.latest_consultation" class="consultation-date">
                                {{ formatDate(patient.latest_consultation.consultation_date) }}
                                <small>{{ patient.latest_consultation.specialty }}</small>
                            </span>
                            <span v-else class="no-consultation">Sem consultas</span>
                        </td>
                        <td><span class="consultation-count">{{ patient.consultation_count }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer v-if="result.meta?.last_page > 1" class="patient-pagination">
            <span>Página {{ result.meta.current_page }} de {{ result.meta.last_page }}</span>
            <div>
                <button type="button" :disabled="loading || result.meta.current_page <= 1" @click="emit('page-change', result.meta.current_page - 1)">
                    Anterior
                </button>
                <button type="button" :disabled="loading || result.meta.current_page >= result.meta.last_page" @click="emit('page-change', result.meta.current_page + 1)">
                    Próxima
                </button>
            </div>
        </footer>
    </section>
</template>

<script setup>
import { RouterLink } from 'vue-router';
import { formatDate } from '../../../shared/utils/date';
import { getInitials } from '../../../shared/utils/initials';

defineProps({
    result: { type: Object, required: true },
    loading: { type: Boolean, default: false },
    totalLabel: { type: String, required: true },
});

const emit = defineEmits(['page-change']);
</script>

<style scoped>
.patient-results { overflow: hidden; }
.patient-results-header { display: flex; justify-content: space-between; align-items: center; padding: 19px 21px; border-bottom: 1px solid #edf0f5; }
.patient-results-header h2 { margin: 0; color: #293a57; font-size: 14px; }
.patient-results-header p { margin: 5px 0 0; color: #8995a8; font-size: 10px; }
.results-count { padding: 6px 9px; border-radius: 20px; color: #375cb3; background: #eef3ff; font-size: 10px; font-weight: 700; }
.patient-table-scroll { overflow-x: auto; }
.patient-table { width: 100%; border-collapse: collapse; text-align: left; }
.patient-table th { padding: 12px 20px; color: #8a96aa; background: #fbfcfe; font-size: 10px; font-weight: 600; white-space: nowrap; }
.patient-table td { padding: 14px 20px; border-top: 1px solid #f0f2f6; color: #4b5a74; font-size: 11px; white-space: nowrap; }
.patient-table tbody tr:hover { background: #fbfcff; }
.patient-name { display: inline-flex; align-items: center; gap: 10px; color: inherit; }
.patient-list-avatar { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 11px; color: #2857d2; background: linear-gradient(135deg, #e1f8ef, #e4eaff); font-size: 10px; font-weight: 700; }
.patient-name strong { display: block; color: #293a57; font-size: 11px; font-weight: 700; }
.patient-name:hover strong { color: #2857d2; }
.patient-name small, .consultation-date small { display: block; margin-top: 4px; color: #97a1b2; font-size: 9px; }
.record-number { padding: 5px 8px; border: 1px solid #edf0f5; border-radius: 6px; color: #61708a; font-size: 10px; }
.consultation-date { color: #465a84; }
.no-consultation { color: #9aa4b4; }
.consultation-count { display: inline-grid; min-width: 26px; height: 26px; place-items: center; border-radius: 8px; color: #3764c8; background: #eff3ff; font-size: 10px; font-weight: 700; }
.patient-empty, .patient-loading { min-height: 210px; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 9px; color: #8490a4; font-size: 11px; }
.patient-empty strong { color: #354560; font-size: 13px; }
.empty-icon { width: 42px; height: 42px; display: grid; place-items: center; margin-bottom: 3px; border-radius: 13px; color: #3a6cd0; background: #eff4ff; }
.empty-icon svg { width: 21px; height: 21px; }
.patient-pagination { display: flex; justify-content: space-between; align-items: center; padding: 13px 20px; border-top: 1px solid #edf0f5; color: #8a96aa; font-size: 10px; }
.patient-pagination div { display: flex; gap: 7px; }
.patient-pagination button { padding: 7px 10px; border: 1px solid #e1e6ef; border-radius: 7px; color: #52617a; background: #fff; font-size: 10px; font-weight: 600; }
.patient-pagination button:hover:not(:disabled) { border-color: #b8c9f0; color: #2857d2; }
.patient-pagination button:disabled { cursor: not-allowed; opacity: .45; }

@media (max-width: 560px) {
    .patient-results-header { padding: 16px; }
    .patient-table th, .patient-table td { padding-right: 14px; padding-left: 14px; }
    .patient-pagination { padding-right: 14px; padding-left: 14px; }
}
</style>
