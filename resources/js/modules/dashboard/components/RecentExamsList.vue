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
                    <tr
                        v-for="(exam, index) in exams"
                        :key="exam.id"
                        :style="{ animationDelay: `${160 + index * 45}ms` }"
                    >
                        <td>
                            <RouterLink class="patient-link" :to="{ name: 'patient-details', params: { id: exam.patient.id } }">
                                <span class="patient-avatar" aria-hidden="true">{{ getInitials(exam.patient.name) }}</span>
                                <span class="patient-copy">
                                    <strong>{{ exam.patient.name }}</strong>
                                    <small>Prontuário {{ exam.patient.medical_record_number }}</small>
                                </span>
                            </RouterLink>
                        </td>
                        <td>
                            <span class="exam-name">{{ exam.name }}</span>
                            <small class="exam-specialty">{{ exam.specialty }}</small>
                        </td>
                        <td><span class="exam-date-value">{{ formatDate(exam.performed_at || exam.consultation_date) }}</span></td>
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

function getInitials(name) {
    return String(name || '')
        .trim()
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('');
}
</script>

<style scoped>
.recent-exams {
    overflow: hidden;
    border-color: #e3eaf3;
    border-radius: 15px;
    box-shadow: 0 4px 18px rgb(34 55 91 / 5%);
    animation: panel-enter 600ms 140ms cubic-bezier(.2, .75, .25, 1) both;
}
.recent-exams :deep(.panel-heading) { padding: 21px 23px; }
.recent-exams :deep(.panel-heading h2) { margin: 0; color: #293a57; font-size: 18px; letter-spacing: -.3px; }
.recent-exams :deep(.panel-heading p) { margin: 6px 0 0; color: #8995a8; font-size: 14px; }
.recent-exams :deep(.panel-action) { display: inline-flex; align-items: center; gap: 7px; color: #4568c2; font-size: 14px; font-weight: 700; transition: gap 180ms ease, color 180ms ease; }
.recent-exams :deep(.panel-action:hover) { gap: 11px; color: #2148e8; }
.table-scroll { overflow-x: auto; overscroll-behavior-x: contain; }
.exams-table { width: 100%; min-width: 720px; border-collapse: separate; border-spacing: 0; text-align: left; }
.exams-table th { padding: 13px 22px; color: #7d8ba1; background: #f7f9fc; font-size: 12px; font-weight: 700; letter-spacing: .55px; text-transform: uppercase; white-space: nowrap; }
.exams-table td { padding: 15px 22px; border-top: 1px solid #edf1f6; color: #4b5a74; font-size: 14px; white-space: nowrap; transition: background-color 160ms ease; }
.exams-table tbody tr { animation: row-enter 420ms ease both; }
.exams-table tbody tr:hover td { background: #f7f9ff; }
.patient-link { display: flex; align-items: center; gap: 11px; color: #354560; }
.patient-link:hover strong { color: #2857d2; }
.patient-avatar { width: 38px; height: 38px; flex: none; display: grid; place-items: center; border: 1px solid #dfe8fb; border-radius: 12px; color: #4568c2; background: linear-gradient(145deg, #f0f5ff, #e8f7f3); font-size: 12px; font-weight: 800; letter-spacing: .3px; transition: transform 180ms ease; }
.patient-link:hover .patient-avatar { transform: scale(1.06); }
.patient-copy { display: grid; gap: 4px; }
.patient-copy strong { transition: color 160ms ease; }
.patient-copy small, .exam-specialty { color: #97a1b2; font-size: 12px; }
.exam-name { color: #354560; font-weight: 700; }
.exam-specialty { display: block; margin-top: 5px; }
.exam-date-value { display: inline-flex; align-items: center; gap: 8px; color: #53637d; font-variant-numeric: tabular-nums; }
.exam-date-value::before { width: 7px; height: 7px; border-radius: 50%; background: #a7b5ca; content: ''; }
.exam-status { display: inline-flex; align-items: center; gap: 7px; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.exam-status::before { width: 6px; height: 6px; border-radius: 50%; background: currentColor; content: ''; }
.status-completed { color: #25836b; background: #e9f8f2; }
.status-scheduled, .status-requested { color: #3569bf; background: #edf3ff; }
.status-cancelled, .status-unknown { color: #aa4852; background: #fff0f1; }
.empty-state { min-height: 150px; display: grid; align-content: center; justify-items: center; gap: 7px; color: #8995a8; font-size: 13px; }
.empty-state strong { color: #354560; font-size: 15px; }

@keyframes panel-enter {
    from { opacity: 0; transform: translateY(14px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes row-enter {
    from { opacity: 0; transform: translateY(7px); }
    to { opacity: 1; transform: translateY(0); }
}

:global(:root[data-theme='dark']) .recent-exams {
    border-color: #2a374b;
    box-shadow: 0 4px 18px rgb(0 0 0 / 14%);
}

:global(:root[data-theme='dark']) .exams-table tbody tr:hover td { background: #1a2639; }
:global(:root[data-theme='dark']) .patient-avatar { border-color: #34445e; color: #b7caff; background: linear-gradient(145deg, #202f4c, #1a3436); }
:global(:root[data-theme='dark']) .exam-date-value { color: #c4cee0; }
:global(:root[data-theme='dark']) .exam-date-value::before { background: #8493aa; }

@media (max-width: 560px) {
    .recent-exams :deep(.panel-heading) { align-items: flex-start; gap: 12px; padding: 17px 16px; }
    .recent-exams :deep(.panel-action) { max-width: 118px; flex: none; justify-content: flex-end; text-align: right; }
    .exams-table th, .exams-table td { padding-right: 15px; padding-left: 15px; }
}

@media (prefers-reduced-motion: reduce) {
    .recent-exams,
    .exams-table tbody tr { animation: none; }
    .recent-exams :deep(.panel-action),
    .patient-avatar,
    .exams-table td { transition: none; }
}
</style>
