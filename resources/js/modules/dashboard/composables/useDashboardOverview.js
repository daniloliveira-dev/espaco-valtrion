import { ref } from 'vue';
import axios from 'axios';
import { DashboardService } from '../services/DashboardService';

export function useDashboardOverview() {
    const dashboard = ref(null);
    const loading = ref(true);
    const error = ref('');

    async function loadDashboard() {
        loading.value = true;
        error.value = '';
        try {
            dashboard.value = await DashboardService.getOverview();
        } catch (requestError) {
            if (axios.isAxiosError(requestError) && requestError.response?.status === 401) {
                error.value = 'Sua sessão expirou. Entre novamente para consultar os dados.';
            } else {
                error.value = 'Não foi possível carregar os dados de pacientes e exames. Tente novamente.';
            }
        } finally {
            loading.value = false;
        }
    }

    return { dashboard, loading, error, loadDashboard };
}
