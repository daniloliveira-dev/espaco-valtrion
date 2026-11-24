import { defineStore } from 'pinia';
import axios from 'axios';
import { AuthService } from '../services/AuthService';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        initialized: false,
        loading: false,
    }),

    actions: {
        async bootstrap() {
            try {
                this.user = await AuthService.currentUser();
            } catch (error) {
                if (axios.isAxiosError(error) && error.response?.status === 401) {
                    this.user = null;
                } else {
                    throw error;
                }
            } finally {
                this.initialized = true;
            }
        },

        async login(credentials) {
            this.loading = true;
            try {
                this.user = await AuthService.login(credentials);
                this.initialized = true;
            } finally {
                this.loading = false;
            }
        },

        async register(details) {
            this.loading = true;
            try {
                this.user = await AuthService.register(details);
                this.initialized = true;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            await AuthService.logout();
            this.user = null;
        },
    },
});
