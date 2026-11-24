<template>
    <section class="dashboard-content patient-details-page">
        <div class="patient-detail-toolbar">
            <RouterLink class="back-to-patients" to="/pacientes">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6M9 12h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
                Voltar para pacientes
            </RouterLink>
        </div>

        <div v-if="loading" class="patient-detail-loading">
            <span class="spinner" aria-hidden="true"></span>
            Carregando prontuário...
        </div>
        <div v-else-if="error" class="patient-error" role="alert">
            <span>{{ error }}</span>
            <RouterLink to="/pacientes">Voltar à lista</RouterLink>
        </div>
        <template v-else-if="patient">
            <header class="patient-detail-heading">
                <div class="patient-detail-avatar">{{ getInitials(patient.name) }}</div>
                <div>
                    <span class="patient-detail-kicker">PRONTUÁRIO {{ patient.medical_record_number }}</span>
                    <h1>{{ patient.name }}</h1>
                    <p>Histórico de consultas e exames</p>
                </div>
            </header>

            <PatientRecordSummary :consultations="patient.consultations" />

            <section class="panel patient-information">
                <header class="patient-section-heading">
                    <span class="patient-section-icon">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.7" /><path d="M4 21a8 8 0 0 1 16 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>
                    </span>
                    <div>
                        <h2>Informações do paciente</h2>
                        <p>Dados cadastrais armazenados no prontuário.</p>
                    </div>
                </header>
                <dl class="patient-information-grid">
                    <div><dt>Nome completo</dt><dd>{{ patient.name }}</dd></div>
                    <div><dt>Número do prontuário</dt><dd>{{ patient.medical_record_number }}</dd></div>
                    <div><dt>Data de nascimento</dt><dd>{{ formatDate(patient.date_of_birth) }}</dd></div>
                    <div><dt>Telefone</dt><dd>{{ patient.phone || 'Não informado' }}</dd></div>
                    <div><dt>E-mail</dt><dd>{{ patient.email || 'Não informado' }}</dd></div>
                </dl>
            </section>

            <div class="consultation-history-heading">
                <div>
                    <h2>Consultas e exames</h2>
                    <p>{{ consultationLabel }}</p>
                </div>
            </div>

            <div v-if="!patient.consultations.length" class="panel patient-no-history">
                Ainda não há consultas registradas para este paciente.
            </div>
            <div v-else class="consultation-history">
                <article v-for="consultation in patient.consultations" :key="consultation.id" class="panel consultation-card">
                    <div class="consultation-card-header">
                        <span class="consultation-date-icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.7" /><path d="M16 3v4M8 3v4M3 10h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>
                        </span>
                        <div class="consultation-heading-copy">
                            <h3>{{ consultation.specialty }}</h3>
                            <p>{{ formatDate(consultation.consultation_date) }} <span aria-hidden="true">·</span> {{ consultation.physician?.name || consultation.physician_name }}</p>
                            <div v-if="consultation.physician" class="physician-meta">
                                <span>Registro: {{ consultation.physician.license_number }}</span>
                                <a v-if="consultation.physician.email" :href="`mailto:${consultation.physician.email}`">{{ consultation.physician.email }}</a>
                                <span v-if="consultation.physician.phone">{{ consultation.physician.phone }}</span>
                            </div>
                        </div>
                        <span class="consultation-date-label">{{ formatDate(consultation.consultation_date) }}</span>
                    </div>

                    <div class="exams-section">
                        <h4>Exames desta consulta <span>{{ consultation.exams.length }}</span></h4>
                        <div v-if="!consultation.exams.length" class="no-exams">Nenhum exame vinculado a esta consulta.</div>
                        <ul v-else class="exam-list">
                            <li v-for="exam in consultation.exams" :key="exam.id">
                                <div class="exam-info">
                                    <span class="exam-name">
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 3h7l5 5v11a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 0v5h5M9 13h6m-6 4h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        {{ exam.name }}
                                    </span>
                                    <small v-if="exam.result_summary" class="exam-result">{{ exam.result_summary }}</small>
                                    <small v-else class="exam-result-pending">Resultado ainda não disponível.</small>
                                </div>
                                <span class="exam-date">{{ exam.performed_at ? formatDate(exam.performed_at) : 'Data não informada' }}</span>
                                <span class="exam-status" :class="getExamStatusClass(exam.status)">{{ getExamStatusLabel(exam.status) }}</span>
                                <ExamDocumentsButton
                                    :patient-id="patient.id"
                                    :exam-id="exam.id"
                                    :exam-name="exam.name"
                                    :documents="exam.documents ?? []"
                                />
                            </li>
                        </ul>
                    </div>
                </article>
            </div>
        </template>
    </section>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import { RouterLink, useRoute } from 'vue-router';
import ExamDocumentsButton from '../components/ExamDocumentsButton.vue';
import PatientRecordSummary from '../components/PatientRecordSummary.vue';
import { PatientService } from '../services/PatientService';
import { formatDate } from '../../../shared/utils/date';
import { getExamStatusClass, getExamStatusLabel } from '../../../shared/utils/examStatus';
import { getInitials } from '../../../shared/utils/initials';

const route = useRoute();
const patient = ref(null);
const loading = ref(true);
const error = ref('');
const consultationLabel = computed(() => {
    const count = patient.value?.consultations.length ?? 0;
    return count === 1 ? '1 consulta registrada' : `${count} consultas registradas`;
});

async function loadPatient() {
    loading.value = true;
    error.value = '';
    patient.value = null;
    try {
        patient.value = await PatientService.getById(route.params.id);
    } catch (requestError) {
        if (axios.isAxiosError(requestError) && requestError.response?.status === 404) {
            error.value = 'Paciente não encontrado.';
        } else if (axios.isAxiosError(requestError) && requestError.response?.status === 401) {
            error.value = 'Sua sessão expirou. Entre novamente para consultar este prontuário.';
        } else {
            error.value = 'Não foi possível carregar o prontuário. Verifique a conexão e tente novamente.';
        }
    } finally {
        loading.value = false;
    }
}

watch(() => route.params.id, loadPatient, { immediate: true });
</script>

<style scoped>
.patient-detail-toolbar { margin-bottom: 20px; }
.back-to-patients { display: inline-flex; align-items: center; gap: 7px; color: #51658f; font-size: 11px; font-weight: 600; }
.back-to-patients:hover { color: #2857d2; }
.back-to-patients svg { width: 16px; height: 16px; transform: rotate(180deg); }
.patient-detail-loading { min-height: 240px; display: flex; justify-content: center; align-items: center; gap: 10px; color: #68778e; font-size: 12px; }
.patient-detail-heading { display: flex; align-items: center; gap: 16px; margin-bottom: 22px; }
.patient-detail-avatar { width: 58px; height: 58px; display: grid; place-items: center; border-radius: 18px; color: #fff; background: var(--brand-gradient); box-shadow: 0 8px 18px rgb(40 87 210 / 16%); font-size: 16px; font-weight: 700; }
.patient-detail-kicker { color: #5974b6; font-size: 9px; font-weight: 700; letter-spacing: 1px; }
.patient-detail-heading h1 { margin: 5px 0 4px; color: #17243b; font-size: 23px; letter-spacing: -.6px; }
.patient-detail-heading p { margin: 0; color: #7a879d; font-size: 11px; }
.patient-information { padding: 20px; }
.patient-section-heading { display: flex; align-items: center; gap: 11px; padding-bottom: 17px; border-bottom: 1px solid #edf0f5; }
.patient-section-icon, .consultation-date-icon { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 10px; color: #315fd0; background: #eef3ff; }
.patient-section-icon svg, .consultation-date-icon svg { width: 18px; height: 18px; }
.patient-section-heading h2, .consultation-history-heading h2 { margin: 0; color: #293a57; font-size: 13px; }
.patient-section-heading p, .consultation-history-heading p { margin: 4px 0 0; color: #8995a8; font-size: 10px; }
.patient-information-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 19px 24px; margin: 0; padding-top: 19px; }
.patient-information-grid div { min-width: 0; }
.patient-information-grid dt { margin-bottom: 6px; color: #8a96aa; font-size: 10px; }
.patient-information-grid dd { overflow-wrap: anywhere; margin: 0; color: #354560; font-size: 11px; font-weight: 600; }
.consultation-history-heading { display: flex; justify-content: space-between; align-items: center; margin: 25px 0 13px; }
.consultation-history { display: grid; gap: 12px; }
.consultation-card { overflow: hidden; }
.consultation-card-header { display: flex; align-items: center; gap: 11px; padding: 15px 19px; border-bottom: 1px solid #edf0f5; }
.consultation-heading-copy { min-width: 0; flex: 1; }
.consultation-heading-copy h3 { margin: 0; color: #293a57; font-size: 12px; }
.consultation-heading-copy p { margin: 5px 0 0; color: #8995a8; font-size: 10px; }
.physician-meta { display: flex; flex-wrap: wrap; gap: 5px 11px; margin-top: 6px; color: #8995a8; font-size: 9px; }
.physician-meta a { color: #5974b6; }
.consultation-date-label { padding: 6px 8px; border-radius: 7px; color: #375cb3; background: #eef3ff; font-size: 9px; font-weight: 700; white-space: nowrap; }
.exams-section { padding: 14px 19px 16px; }
.exams-section h4 { display: flex; align-items: center; gap: 8px; margin: 0 0 10px; color: #64728a; font-size: 10px; font-weight: 700; }
.exams-section h4 span { display: grid; min-width: 18px; height: 18px; place-items: center; border-radius: 6px; color: #315fd0; background: #eef3ff; font-size: 9px; }
.exam-list { display: grid; gap: 7px; margin: 0; padding: 0; list-style: none; }
.exam-list li { display: flex; align-items: center; gap: 12px; min-height: 35px; padding: 7px 9px; border-radius: 7px; background: #fafbfe; }
.exam-info { display: grid; flex: 1; gap: 5px; min-width: 0; }
.exam-name { display: inline-flex; align-items: center; gap: 8px; min-width: 0; color: #3f4f69; font-size: 10px; font-weight: 600; }
.exam-name svg { width: 15px; height: 15px; flex: none; color: #5974b6; }
.exam-result, .exam-result-pending { color: #697991; font-size: 9px; white-space: normal; }
.exam-result-pending { color: #9aa4b4; }
.exam-date { color: #8995a8; font-size: 9px; white-space: nowrap; }
.exam-status { padding: 4px 7px; border-radius: 20px; color: #3569bf; background: #edf3ff; font-size: 9px; font-weight: 700; white-space: nowrap; }
.exam-status.status-completed { color: #25836b; background: #e9f8f2; }
.exam-status.status-cancelled { color: #aa4852; background: #fff0f1; }
.patient-no-history { padding: 28px; color: #8995a8; text-align: center; font-size: 11px; }

@media (max-width: 720px) {
    .patient-information-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .consultation-date-label { display: none; }
}
@media (max-width: 480px) {
    .patient-detail-heading h1 { font-size: 19px; }
    .patient-information-grid { grid-template-columns: 1fr; gap: 15px; }
    .consultation-card-header, .exams-section { padding-right: 14px; padding-left: 14px; }
    .exam-list li { flex-wrap: wrap; }
    .exam-info { flex-basis: 100%; }
}
</style>
