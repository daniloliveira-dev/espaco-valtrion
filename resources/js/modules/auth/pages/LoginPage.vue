<template>
    <div>
        <span class="eyebrow">Bem-vindo de volta</span>
        <h2 style="margin-top:12px">Acesse sua conta</h2>
        <p class="auth-subtitle">Entre com seus dados para continuar na plataforma.</p>

        <div v-if="serverError" class="alert" role="alert">{{ serverError }}</div>

        <form @submit="onSubmit">
            <Field name="email" v-slot="{ field, errorMessage }">
                <AppField
                    v-bind="field"
                    id="login-email"
                    label="E-mail profissional"
                    type="email"
                    placeholder="voce@hospital.com.br"
                    autocomplete="email"
                    :error="errorMessage"
                />
            </Field>
            <Field name="password" v-slot="{ field, errorMessage }">
                <AppField
                    v-bind="field"
                    id="login-password"
                    label="Senha"
                    type="password"
                    placeholder="Sua senha"
                    autocomplete="current-password"
                    :error="errorMessage"
                />
            </Field>

            <div class="form-options">
                <label class="check-label">
                    <input v-model="remember" type="checkbox">
                    <span>Manter conectado</span>
                </label>
                <span title="Recuperação de senha será adicionada em breve">Esqueceu a senha?</span>
            </div>

            <button class="primary-button" type="submit" :disabled="loading">
                <span v-if="loading" class="spinner" aria-hidden="true"></span>
                <span>{{ loading ? 'Entrando...' : 'Entrar na plataforma' }}</span>
                <svg v-if="!loading" width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </form>

        <p class="auth-switch">Ainda não tem uma conta? <RouterLink class="text-link" to="/register">Criar conta</RouterLink></p>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Field, useForm } from 'vee-validate';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import axios from 'axios';
import AppField from '../../../shared/components/AppField.vue';
import { useAuthStore } from '../stores/authStore';
import { loginSchema } from '../validators/authValidator';

const auth = useAuthStore();
const route = useRoute();
const router = useRouter();
const remember = ref(false);
const serverError = ref('');
const { handleSubmit, setErrors } = useForm({
    validationSchema: loginSchema,
    initialValues: { email: '', password: '' },
});
const loading = ref(false);

const onSubmit = handleSubmit(async (values) => {
    serverError.value = '';
    loading.value = true;
    try {
        await auth.login({ ...values, remember: remember.value });
        const requestedDestination = typeof route.query.redirect === 'string' ? route.query.redirect : '/dashboard';
        const destinationUrl = new URL(requestedDestination, window.location.origin);
        const destination = destinationUrl.origin === window.location.origin
            ? `${destinationUrl.pathname}${destinationUrl.search}${destinationUrl.hash}`
            : '/dashboard';
        await router.replace(destination);
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            setErrors(error.response.data.errors ?? {});
        } else {
            serverError.value = 'Não foi possível entrar. Verifique sua conexão e tente novamente.';
        }
    } finally {
        loading.value = false;
    }
});
</script>
