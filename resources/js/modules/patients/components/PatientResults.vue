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
                    <tr
                        v-for="(patient, index) in result.data"
                        :key="patient.id"
                        :style="{ animationDelay: `${index * 35}ms` }"
                    >
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
.patient-results { overflow: hidden; border-radius: 15px; box-shadow: 0 6px 22px rgb(34 55 91 / 5%); }
.patient-results-header { display: flex; justify-content: space-between; align-items: center; padding: 21px 23px; border-bottom: 1px solid #edf0f5; }
.patient-results-header h2 { margin: 0; color: #293a57; font-size: 17px; letter-spacing: -.2px; }
.patient-results-header p { margin: 6px 0 0; color: #8995a8; font-size: 14px; }
.results-count { padding: 7px 11px; border: 1px solid #dfe8fb; border-radius: 999px; color: #375cb3; background: #f2f6ff; font-size: 13px; font-weight: 700; white-space: nowrap; }
.patient-table-scroll { overflow-x: auto; overscroll-behavior-x: contain; }
.patient-table { width: 100%; min-width: 760px; border-collapse: separate; border-spacing: 0; text-align: left; }
.patient-table th { padding: 13px 22px; color: #7d8ba1; background: #f7f9fc; font-size: 12px; font-weight: 700; letter-spacing: .55px; text-transform: uppercase; white-space: nowrap; }
.patient-table td { padding: 15px 22px; border-top: 1px solid #edf1f6; color: #4b5a74; font-size: 14px; white-space: nowrap; transition: background-color 160ms ease; }
.patient-table tbody tr { animation: patient-row-enter 380ms ease both; }
.patient-table tbody tr:hover td { background: #f5f8ff; }
.patient-name { display: inline-flex; align-items: center; gap: 12px; color: inherit; }
.patient-name:focus-visible { border-radius: 8px; outline: 3px solid rgb(57 124 219 / 32%); outline-offset: 4px; }
.patient-list-avatar { width: 36px; height: 36px; display: grid; place-items: center; border-radius: 11px; color: #2857d2; background: linear-gradient(135deg, #e1f8ef, #e4eaff); font-size: 13px; font-weight: 700; }
.patient-name strong { display: block; color: #293a57; font-size: 14px; font-weight: 700; transition: color 160ms ease; }
.patient-name:hover strong { color: #2857d2; }
.patient-name small, .consultation-date small { display: block; margin-top: 4px; color: #8995a8; font-size: 13px; }
.record-number { padding: 6px 9px; border: 1px solid #e6ebf3; border-radius: 8px; color: #61708a; font-size: 13px; font-variant-numeric: tabular-nums; }
.consultation-date { color: #465a84; }
.no-consultation { color: #9aa4b4; }
.consultation-count { display: inline-grid; min-width: 29px; height: 29px; place-items: center; border: 1px solid #e1e9fb; border-radius: 9px; color: #3764c8; background: #eff3ff; font-size: 13px; font-weight: 700; }
.patient-empty, .patient-loading { min-height: 230px; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 9px; color: #8490a4; font-size: 14px; }
.patient-empty strong { color: #354560; font-size: 16px; }
.empty-icon { width: 48px; height: 48px; display: grid; place-items: center; margin-bottom: 4px; border-radius: 15px; color: #3a6cd0; background: #eff4ff; }
.empty-icon svg { width: 21px; height: 21px; }
.patient-pagination { display: flex; justify-content: space-between; align-items: center; padding: 15px 22px; border-top: 1px solid #edf0f5; color: #7d8ba1; font-size: 14px; }
.patient-pagination div { display: flex; gap: 7px; }
.patient-pagination button { padding: 8px 12px; border: 1px solid #e1e6ef; border-radius: 8px; color: #52617a; background: #fff; font-size: 13px; font-weight: 600; transition: border-color 160ms ease, color 160ms ease, background-color 160ms ease, transform 160ms ease; }
.patient-pagination button:hover:not(:disabled) { transform: translateY(-1px); border-color: #b8c9f0; color: #2857d2; background: #f7f9ff; }
.patient-pagination button:disabled { cursor: not-allowed; opacity: .45; }

@keyframes patient-row-enter {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

:global(:root[data-theme='dark']) .patient-results {
    border-color: #2a374b;
    box-shadow: 0 6px 22px rgb(0 0 0 / 15%);
}

:global(:root[data-theme='dark']) .results-count {
    border-color: #334666;
    color: #b7caff;
    background: #202f4c;
}

:global(:root[data-theme='dark']) .patient-table tbody tr:hover td { background: #1c2a40; }

:global(:root[data-theme='dark']) .patient-pagination button:hover:not(:disabled) {
    border-color: #556d9c;
    color: #c4d2ff;
    background: #202f4c;
}

@media (max-width: 560px) {
    .patient-results-header { padding: 17px 16px; }
    .patient-table th, .patient-table td { padding-right: 15px; padding-left: 15px; }
    .patient-pagination { padding-right: 15px; padding-left: 15px; }
}

@media (prefers-reduced-motion: reduce) {
    .patient-table tbody tr { animation: none; }
    .patient-table td,
    .patient-name strong,
    .patient-pagination button { transition: none; }
}
</style>
