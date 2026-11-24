import api from '../../../shared/services/api';

export const PatientService = {
    async search(filters, page = 1) {
        const params = Object.fromEntries(
            Object.entries({ ...filters, page }).filter(([, value]) => value !== '' && value !== null && value !== undefined),
        );
        const { data } = await api.get('/patients', { params });
        return data;
    },

    async getById(id) {
        const { data } = await api.get(`/patients/${id}`);
        return data.data;
    },
};
