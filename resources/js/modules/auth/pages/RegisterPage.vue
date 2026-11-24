<template>
    <div>
        <span class="eyebrow">Comece por aqui</span>
        <h2 style="margin-top:12px">Crie sua conta</h2>
        <p class="auth-subtitle">Acesse uma forma mais simples de organizar o cuidado.</p>

        <div v-if="serverError" class="alert" role="alert">{{ serverError }}</div>

        <form @submit="onSubmit">
            <Field name="name" v-slot="{ field, errorMessage }">
                <AppField
                    v-bind="field"
                    id="register-name"
                    label="Nome completo"
                    placeholder="Como podemos chamar você?"
                    autocomplete="name"
                    :error="errorMessage"
                />
            </Field>
            <Field name="email" v-slot="{ field, errorMessage }">
                <AppField
                    v-bind="field"
                    id="register-email"
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
                    id="register-password"
                    label="Senha"
                    type="password"
                    placeholder="Pelo menos 8 caracteres"
                    autocomplete="new-password"
                    :error="errorMessage"
                />
            </Field>
            <Field name="password_confirmation" v-slot="{ field, errorMessage }">
                <AppField
                    v-bind="field"
                    id="register-confirm-password"
                    label="Confirme sua senha"
                    type="password"
                    placeholder="Digite a senha novamente"
                    autocomplete="new-password"
                    :error="errorMessage"
                />
            </Field>

            <button class="primary-button" type="submit" :disabled="loading">
                <span v-if="loading" class="spinner" aria-hidden="true"></span>
                <span>{{ loading ? 'Criando conta...' : 'Criar minha conta' }}</span>
                <svg v-if="!loading" width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </form>

        <p class="auth-switch">Já tem uma conta? <RouterLink class="text-link" to="/login">Entrar</RouterLink></p>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Field, useForm } from 'vee-validate';
import { useRouter, RouterLink } from 'vue-router';
import axios from 'axios';
import AppField from '../../../shared/components/AppField.vue';
import { useAuthStore } from '../stores/authStore';
import { registerSchema } from '../validators/authValidator';

const auth = useAuthStore();
const router = useRouter();
const serverError = ref('');
const loading = ref(false);
const { handleSubmit, setErrors } = useForm({
    validationSchema: registerSchema,
    initialValues: { name: '', email: '', password: '', password_confirmation: '' },
});

const onSubmit = handleSubmit(async (values) => {
    serverError.value = '';
    loading.value = true;
    try {
        await auth.register(values);
        await router.replace('/dashboard');
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            setErrors(error.response.data.errors ?? {});
        } else {
            serverError.value = 'Não foi possível criar sua conta. Tente novamente em instantes.';
        }
    } finally {
        loading.value = false;
    }
});
</script>
