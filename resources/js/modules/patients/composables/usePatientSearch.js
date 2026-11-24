import { computed, reactive, ref } from 'vue';
import axios from 'axios';
import { PatientService } from '../services/PatientService';

export function usePatientSearch() {
    const filters = reactive({ name: '', exam: '', date: '' });
    const result = ref({ data: [], meta: null });
    const loading = ref(false);
    const error = ref('');
    const currentPage = computed(() => result.value.meta?.current_page ?? 1);
    const totalLabel = computed(() => {
        const total = result.value.meta?.total ?? 0;
        return total === 1 ? '1 paciente encontrado' : `${total} pacientes encontrados`;
    });

    async function loadPatients(page = 1) {
        loading.value = true;
        error.value = '';
        try {
            result.value = await PatientService.search(filters, page);
        } catch (requestError) {
            if (axios.isAxiosError(requestError) && requestError.response?.status === 401) {
                error.value = 'Sua sessão expirou. Entre novamente para consultar os pacientes.';
            } else {
                error.value = 'Não foi possível carregar os pacientes. Verifique a conexão e tente novamente.';
            }
        } finally {
            loading.value = false;
        }
    }

    function applyFilters() {
        return loadPatients(1);
    }

    function clearFilters() {
        filters.name = '';
        filters.exam = '';
        filters.date = '';
        return loadPatients(1);
    }

    return {
        filters,
        result,
        loading,
        error,
        currentPage,
        totalLabel,
        loadPatients,
        applyFilters,
        clearFilters,
    };
}
