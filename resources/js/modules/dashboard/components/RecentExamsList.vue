<template>
    <section class="panel recent-exams">
        <header class="panel-heading">
            <div>
                <h2>Exames recentes</h2>
                <p>Atividade clínica vinculada aos prontuários</p>
            </div>
            <RouterLink class="panel-action" to="/pacientes">Consultar pacientes <span aria-hidden="true">→</span></RouterLink>
        </header>

        <div v-if="!exams.length" class="empty-state">
            <strong>Nenhum exame registrado</strong>
            <span>Os exames cadastrados aparecerão aqui.</span>
        </div>
        <div v-else class="table-scroll">
            <table class="exams-table">
                <thead>
                    <tr>
                        <th scope="col">Paciente</th>
                        <th scope="col">Exame</th>
                        <th scope="col">Data</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="exam in exams" :key="exam.id">
                        <td>
                            <RouterLink class="patient-link" :to="{ name: 'patient-details', params: { id: exam.patient.id } }">
                                <strong>{{ exam.patient.name }}</strong>
                                <small>Prontuário {{ exam.patient.medical_record_number }}</small>
                            </RouterLink>
                        </td>
                        <td>
                            <span class="exam-name">{{ exam.name }}</span>
                            <small class="exam-specialty">{{ exam.specialty }}</small>
                        </td>
                        <td>{{ formatDate(exam.performed_at || exam.consultation_date) }}</td>
                        <td>
                            <span class="exam-status" :class="getExamStatusClass(exam.status)">
                                {{ getExamStatusLabel(exam.status) }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>

<script setup>
import { RouterLink } from 'vue-router';
import { formatDate } from '../../../shared/utils/date';
import { getExamStatusClass, getExamStatusLabel } from '../../../shared/utils/examStatus';

defineProps({
    exams: { type: Array, required: true },
});
</script>

<style scoped>
.recent-exams { overflow: hidden; }
.recent-exams :deep(.panel-heading) { padding: 18px 20px; }
.recent-exams :deep(.panel-heading h2) { margin: 0; color: #293a57; font-size: 14px; }
.recent-exams :deep(.panel-heading p) { margin: 5px 0 0; color: #8995a8; font-size: 10px; }
.recent-exams :deep(.panel-action) { display: inline-flex; align-items: center; gap: 7px; color: #4568c2; font-size: 10px; font-weight: 700; }
.table-scroll { overflow-x: auto; }
.exams-table { width: 100%; border-collapse: collapse; text-align: left; }
.exams-table th { padding: 12px 20px; color: #8a96aa; background: #fbfcfe; font-size: 10px; font-weight: 600; white-space: nowrap; }
.exams-table td { padding: 13px 20px; border-top: 1px solid #f0f2f6; color: #4b5a74; font-size: 10px; white-space: nowrap; }
.patient-link { display: grid; gap: 4px; color: #354560; }
.patient-link:hover strong { color: #2857d2; }
.patient-link small, .exam-specialty { color: #97a1b2; font-size: 9px; }
.exam-name { color: #354560; font-weight: 600; }
.exam-specialty { display: block; margin-top: 4px; }
.exam-status { display: inline-block; padding: 5px 8px; border-radius: 20px; font-size: 9px; font-weight: 700; }
.status-completed { color: #25836b; background: #e9f8f2; }
.status-scheduled, .status-requested { color: #3569bf; background: #edf3ff; }
.status-cancelled, .status-unknown { color: #aa4852; background: #fff0f1; }
.empty-state { min-height: 150px; display: grid; align-content: center; justify-items: center; gap: 7px; color: #8995a8; font-size: 11px; }
.empty-state strong { color: #354560; font-size: 13px; }

@media (max-width: 560px) {
    .exams-table th, .exams-table td { padding-right: 13px; padding-left: 13px; }
    .recent-exams :deep(.panel-heading) { padding: 16px; }
}
</style>
