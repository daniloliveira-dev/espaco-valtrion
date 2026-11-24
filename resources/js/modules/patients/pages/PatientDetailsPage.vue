<template>
    <section class="dashboard-content patient-details-page">
        <nav class="patient-detail-toolbar" aria-label="Navegação do prontuário">
            <RouterLink class="back-to-patients" to="/pacientes">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 12H5m7 7-7-7 7-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" /></svg>
                Pacientes
            </RouterLink>
            <span class="breadcrumb-divider" aria-hidden="true">/</span>
            <span class="breadcrumb-current" aria-current="page">Prontuário</span>
        </nav>

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
                <div class="patient-detail-avatar" aria-hidden="true">{{ getInitials(patient.name) }}</div>
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
                <article
                    v-for="(consultation, index) in patient.consultations"
                    :key="consultation.id"
                    class="panel consultation-card"
                    :style="{ animationDelay: `${120 + index * 55}ms` }"
                >
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
.patient-details-page { max-width: 1440px; }
.patient-detail-toolbar { display: flex; align-items: center; gap: 11px; margin-bottom: 19px; animation: detail-enter 360ms ease both; }
.back-to-patients { display: inline-flex; align-items: center; gap: 8px; padding: 7px 11px 7px 8px; border: 1px solid transparent; border-radius: 9px; color: #51658f; font-size: 14px; font-weight: 700; transition: color 160ms ease, background-color 160ms ease, transform 160ms ease; }
.back-to-patients:hover { transform: translateX(-2px); color: #2857d2; background: #f0f4ff; }
.back-to-patients:focus-visible { outline: 3px solid rgb(57 124 219 / 32%); outline-offset: 2px; }
.back-to-patients svg { width: 17px; height: 17px; }
.breadcrumb-divider { color: #b7c1d0; }
.breadcrumb-current { color: #7d8ba1; font-size: 14px; font-weight: 600; }
.patient-detail-loading { min-height: 240px; display: flex; justify-content: center; align-items: center; gap: 10px; color: #68778e; font-size: 14px; }
.patient-detail-heading { position: relative; isolation: isolate; display: flex; align-items: center; gap: 18px; overflow: hidden; margin-bottom: 20px; padding: 24px; border: 1px solid #e4ebf5; border-radius: 17px; background: radial-gradient(circle at 95% 15%, rgb(64 213 165 / 11%), transparent 29%), linear-gradient(115deg, #fff 0%, #f8fbff 100%); box-shadow: 0 7px 24px rgb(34 55 91 / 6%); animation: detail-enter 440ms 50ms cubic-bezier(.2, .75, .25, 1) both; }
.patient-detail-heading::after { position: absolute; z-index: -1; top: -63px; right: 12%; width: 180px; height: 180px; border: 1px solid rgb(76 116 191 / 10%); border-radius: 50%; content: ''; pointer-events: none; }
.patient-detail-avatar { width: 68px; height: 68px; flex: none; display: grid; place-items: center; border: 3px solid rgb(255 255 255 / 72%); border-radius: 21px; color: #fff; background: var(--brand-gradient); box-shadow: 0 9px 22px rgb(40 87 210 / 22%); font-size: 20px; font-weight: 800; letter-spacing: .4px; }
.patient-detail-kicker { color: #5974b6; font-size: 12px; font-weight: 800; letter-spacing: 1.1px; }
.patient-detail-heading h1 { margin: 6px 0 4px; color: #17243b; font-size: 27px; letter-spacing: -.8px; }
.patient-detail-heading p { margin: 0; color: #7a879d; font-size: 14px; }
.patient-information { padding: 21px; border-radius: 15px; box-shadow: 0 5px 20px rgb(34 55 91 / 4%); animation: detail-enter 440ms 100ms ease backwards; transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease; }
.patient-information:hover { transform: translateY(-2px); border-color: #d7e1ef; box-shadow: 0 10px 24px rgb(34 55 91 / 8%); }
.patient-section-heading { display: flex; align-items: center; gap: 12px; padding-bottom: 17px; border-bottom: 1px solid #edf0f5; }
.patient-section-icon, .consultation-date-icon { width: 38px; height: 38px; flex: none; display: grid; place-items: center; border-radius: 12px; color: #315fd0; background: #eef3ff; }
.patient-section-icon svg, .consultation-date-icon svg { width: 19px; height: 19px; }
.patient-section-heading h2, .consultation-history-heading h2 { margin: 0; color: #293a57; font-size: 16px; letter-spacing: -.2px; }
.patient-section-heading p, .consultation-history-heading p { margin: 5px 0 0; color: #8995a8; font-size: 14px; }
.patient-information-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px 25px; margin: 0; padding-top: 20px; }
.patient-information-grid div { min-width: 0; }
.patient-information-grid dt { margin-bottom: 7px; color: #8491a6; font-size: 13px; }
.patient-information-grid dd { overflow-wrap: anywhere; margin: 0; color: #354560; font-size: 14px; font-weight: 600; }
.consultation-history-heading { display: flex; justify-content: space-between; align-items: center; margin: 29px 0 14px; animation: detail-enter 400ms 140ms ease both; }
.consultation-history { display: grid; gap: 14px; }
.consultation-card { overflow: hidden; border-radius: 14px; box-shadow: 0 4px 16px rgb(34 55 91 / 4%); animation: detail-enter 460ms ease backwards; transition: transform 190ms ease, border-color 190ms ease, box-shadow 190ms ease; }
.consultation-card:hover { transform: translateY(-2px); border-color: #d7e1ef; box-shadow: 0 10px 24px rgb(34 55 91 / 8%); }
.consultation-card-header { display: flex; align-items: center; gap: 12px; padding: 17px 20px; border-bottom: 1px solid #edf0f5; }
.consultation-heading-copy { min-width: 0; flex: 1; }
.consultation-heading-copy h3 { margin: 0; color: #293a57; font-size: 16px; }
.consultation-heading-copy p { margin: 6px 0 0; color: #8995a8; font-size: 14px; }
.physician-meta { display: flex; flex-wrap: wrap; gap: 5px 12px; margin-top: 7px; color: #8995a8; font-size: 13px; }
.physician-meta a { color: #5974b6; transition: color 150ms ease; }
.physician-meta a:hover { color: #2857d2; text-decoration: underline; text-underline-offset: 2px; }
.consultation-date-label { padding: 7px 10px; border: 1px solid #dfe8fb; border-radius: 8px; color: #375cb3; background: #f2f6ff; font-size: 13px; font-weight: 700; white-space: nowrap; }
.exams-section { padding: 16px 20px 19px; }
.exams-section h4 { display: flex; align-items: center; gap: 9px; margin: 0 0 11px; color: #64728a; font-size: 14px; font-weight: 700; }
.exams-section h4 span { display: grid; min-width: 21px; height: 21px; place-items: center; border-radius: 7px; color: #315fd0; background: #eef3ff; font-size: 12px; }
.exam-list { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }
.exam-list li { display: flex; align-items: center; gap: 13px; min-height: 48px; padding: 9px 11px; border: 1px solid transparent; border-radius: 10px; background: #f8fafe; transition: transform 170ms ease, border-color 170ms ease, background-color 170ms ease; }
.exam-list li:hover { transform: translateX(2px); border-color: #e3eafa; background: #f2f6ff; }
.exam-info { display: grid; flex: 1; gap: 5px; min-width: 0; }
.exam-name { display: inline-flex; align-items: center; gap: 8px; min-width: 0; color: #3f4f69; font-size: 14px; font-weight: 700; }
.exam-name svg { width: 16px; height: 16px; flex: none; color: #5974b6; }
.exam-result, .exam-result-pending { color: #697991; font-size: 13px; white-space: normal; }
.exam-result-pending { color: #9aa4b4; }
.exam-date { color: #8995a8; font-size: 13px; font-variant-numeric: tabular-nums; white-space: nowrap; }
.exam-status { padding: 5px 9px; border-radius: 999px; color: #3569bf; background: #edf3ff; font-size: 12px; font-weight: 700; white-space: nowrap; }
.exam-status.status-completed { color: #25836b; background: #e9f8f2; }
.exam-status.status-cancelled { color: #aa4852; background: #fff0f1; }
.patient-no-history { padding: 32px; border-radius: 14px; color: #8995a8; text-align: center; font-size: 14px; }

@keyframes detail-enter {
    from { opacity: 0; transform: translateY(9px); }
    to { opacity: 1; transform: translateY(0); }
}

:global(:root[data-theme='dark']) .patient-detail-heading {
    border-color: #2a374b;
    background: radial-gradient(circle at 95% 15%, rgb(64 213 165 / 8%), transparent 29%), linear-gradient(115deg, #151f30 0%, #172338 100%);
    box-shadow: 0 7px 24px rgb(0 0 0 / 16%);
}

:global(:root[data-theme='dark']) .patient-detail-heading::after { border-color: rgb(157 184 255 / 11%); }
:global(:root[data-theme='dark']) .patient-information,
:global(:root[data-theme='dark']) .consultation-card { box-shadow: 0 4px 16px rgb(0 0 0 / 14%); }
:global(:root[data-theme='dark']) .patient-information:hover,
:global(:root[data-theme='dark']) .consultation-card:hover { border-color: #40516d; box-shadow: 0 10px 24px rgb(0 0 0 / 24%); }
:global(:root[data-theme='dark']) .back-to-patients:hover { color: #b7caff; background: #202f4c; }
:global(:root[data-theme='dark']) .consultation-date-label { border-color: #334666; color: #b7caff; background: #202f4c; }
:global(:root[data-theme='dark']) .exam-list li:hover { border-color: #34445e; background: #202f4c; }

@media (max-width: 720px) {
    .patient-information-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .consultation-date-label { display: none; }
}
@media (max-width: 480px) {
    .patient-detail-heading { gap: 14px; padding: 18px; border-radius: 14px; }
    .patient-detail-avatar { width: 56px; height: 56px; border-radius: 17px; font-size: 17px; }
    .patient-detail-heading h1 { font-size: 21px; overflow-wrap: anywhere; }
    .patient-information { padding: 16px; }
    .patient-information-grid { grid-template-columns: 1fr; gap: 16px; }
    .consultation-card-header, .exams-section { padding-right: 14px; padding-left: 14px; }
    .exam-list li { flex-wrap: wrap; }
    .exam-info { flex-basis: 100%; }
}

@media (prefers-reduced-motion: reduce) {
    .patient-detail-toolbar,
    .patient-detail-heading,
    .patient-information,
    .consultation-history-heading,
    .consultation-card { animation: none; }
    .back-to-patients,
    .patient-information,
    .consultation-card,
    .physician-meta a,
    .exam-list li { transition: none; }
}
</style>
