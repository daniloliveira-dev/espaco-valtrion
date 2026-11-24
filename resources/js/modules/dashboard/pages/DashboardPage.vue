<template>
    <section class="dashboard-content">
        <div class="welcome-line">
            <div>
                <h1>Bom dia, {{ firstName }} <span aria-hidden="true">☀️</span></h1>
                <p>Confira o resumo de hoje e acompanhe sua equipe.</p>
            </div>
            <span class="date-pill">{{ today }}</span>
        </div>

        <div class="demo-notice">
            <span aria-hidden="true">ⓘ</span>
            Painel demonstrativo — os dados exibidos são fictícios e não representam pacientes reais.
        </div>

        <div v-if="error" class="dashboard-error" role="alert">
            <span>{{ error }}</span>
            <button type="button" @click="loadDashboard">Tentar novamente</button>
        </div>
        <div v-else-if="loading" class="dashboard-loading" aria-label="Carregando painel">
            <span class="spinner" aria-hidden="true"></span>
            Carregando indicadores...
        </div>
        <template v-else>
            <div class="stat-grid">
                <article v-for="(metric, index) in dashboard.metrics" :key="metric.label" class="stat-card">
                    <div class="stat-head">
                        <span>{{ metric.label }}</span>
                        <span class="stat-icon">
                            <svg v-if="index === 0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-5A4.5 4.5 0 0 0 2 18.5V20m7-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-1.5a4.5 4.5 0 0 0-3.5-4.38M16 2.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>
                            <svg v-else-if="index === 1" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.7" /><path d="M16 3v4M8 3v4M3 10h18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" /></svg>
                            <svg v-else-if="index === 2" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14m-7-7h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg>
                            <svg v-else viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14m-9 0v-5h4v5m-6-11h.01M12 10h.01M15 10h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                    <div class="stat-value">{{ metric.value }}</div>
                    <div class="stat-foot"><span v-if="index === 0">↗ {{ metric.detail }}</span><template v-else>{{ metric.detail }}</template></div>
                </article>
            </div>

            <div class="dashboard-grid">
                <section class="panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Atendimentos da semana</h2>
                            <p>Volume de consultas realizadas</p>
                        </div>
                        <button class="panel-action" type="button">Esta semana <span aria-hidden="true">⌄</span></button>
                    </div>
                    <div class="chart-area">
                        <svg class="activity-chart" viewBox="0 0 560 190" role="img" aria-label="Gráfico de atendimentos da semana">
                            <defs>
                                <linearGradient id="valtrix-chart-gradient" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#40d5a5" />
                                    <stop offset="100%" stop-color="#2148e8" />
                                </linearGradient>
                            </defs>
                            <line v-for="line in 4" :key="line" x1="30" :y1="line * 38" x2="550" :y2="line * 38" stroke="#edf2f1" stroke-dasharray="4 5" />
                            <g v-for="(item, index) in dashboard.weekly" :key="item.day">
                                <rect
                                    :x="52 + index * 72"
                                    :y="170 - item.capacity * 2.9"
                                    width="20"
                                    :height="item.capacity * 2.9"
                                    rx="6"
                                    fill="#eaf4f1"
                                />
                                <rect
                                    :x="52 + index * 72"
                                    :y="170 - item.visits * 2.9"
                                    width="20"
                                    :height="item.visits * 2.9"
                                    rx="6"
                                    fill="url(#valtrix-chart-gradient)"
                                />
                            </g>
                        </svg>
                        <div class="chart-labels">
                            <span v-for="item in dashboard.weekly" :key="item.day">{{ item.day }}</span>
                        </div>
                        <div class="chart-legend">
                            <span class="legend-key"><i class="legend-dot"></i> Atendimentos</span>
                            <span class="legend-key"><i class="legend-dot light"></i> Capacidade</span>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="panel-heading">
                        <div>
                            <h2>Próximos atendimentos</h2>
                            <p>{{ dashboard.appointments.length }} agendamentos hoje</p>
                        </div>
                        <button class="panel-action" type="button">Ver agenda <span aria-hidden="true">→</span></button>
                    </div>
                    <div class="appointments">
                        <article v-for="appointment in dashboard.appointments" :key="appointment.time" class="appointment">
                            <span class="appointment-time">{{ appointment.time }}</span>
                            <span class="appointment-avatar">{{ appointment.initials }}</span>
                            <span class="appointment-info">
                                <strong>{{ appointment.patient }}</strong>
                                <span>{{ appointment.specialty }}</span>
                            </span>
                            <i class="appointment-status" :class="{ pending: appointment.status === 'pending' }" :aria-label="appointment.status === 'pending' ? 'Pendente' : 'Confirmado'"></i>
                        </article>
                    </div>
                    <a class="schedule-footer" href="#" @click.prevent="showScheduleNotice">Abrir agenda completa →</a>
                </section>
            </div>
        </template>
    </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { useAuthStore } from '../../auth/stores/authStore';
import { DashboardService } from '../services/DashboardService';

const auth = useAuthStore();
const dashboard = ref(null);
const loading = ref(true);
const error = ref('');
const notice = ref('');

const firstName = computed(() => auth.user?.name?.split(/\s+/)[0] ?? 'equipe');
const today = new Intl.DateTimeFormat('pt-BR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
}).format(new Date());

async function loadDashboard() {
    loading.value = true;
    error.value = '';
    try {
        dashboard.value = await DashboardService.getOverview();
    } catch (requestError) {
        if (axios.isAxiosError(requestError) && requestError.response?.status === 401) {
            error.value = 'Sua sessão expirou. Atualize a página para entrar novamente.';
        } else {
            error.value = 'Não foi possível carregar os indicadores. Verifique sua conexão e tente novamente.';
        }
    } finally {
        loading.value = false;
    }
}

function showScheduleNotice() {
    notice.value = 'O módulo de agenda estará disponível em breve.';
    setTimeout(() => { notice.value = ''; }, 2800);
}

onMounted(loadDashboard);
</script>

<style scoped>
.demo-notice { display: flex; align-items: center; gap: 8px; margin: -12px 0 20px; color: #758198; font-size: 10px; }
.demo-notice span { color: #347ac5; font-size: 13px; }
.dashboard-loading { min-height: 260px; display: flex; align-items: center; justify-content: center; gap: 11px; color: #68778e; font-size: 12px; }
.dashboard-error { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 15px; border: 1px solid #f1d4d5; border-radius: 9px; color: #9c3d44; background: #fff7f7; font-size: 12px; }
.dashboard-error button { border: 0; color: #2857d2; background: transparent; font-size: 11px; font-weight: 700; white-space: nowrap; }
.activity-chart { width: 100%; height: auto; overflow: visible; }
.activity-chart line { vector-effect: non-scaling-stroke; }
.activity-chart rect { vector-effect: non-scaling-stroke; }
.schedule-footer { cursor: pointer; }
.schedule-footer:hover { background: #f7fbfa; }
</style>
