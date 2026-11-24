import api from '../../../shared/services/api';

export const DashboardService = {
    async getOverview() {
        const { data } = await api.get('/dashboard');
        return data;
    },
};
