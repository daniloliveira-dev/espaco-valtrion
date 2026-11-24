import api from '../../../shared/services/api';

export const AuthService = {
    async login(credentials) {
        const { data } = await api.post('/auth/login', credentials);
        return data.user;
    },

    async register(details) {
        const { data } = await api.post('/auth/register', details);
        return data.user;
    },

    async currentUser() {
        const { data } = await api.get('/auth/user');
        return data.user;
    },

    async logout() {
        await api.post('/auth/logout');
    },
};
