<template>
    <main class="unavailable">
        <ValtrixLogo href="/login" />
        <p class="eyebrow">ÁREA DO CLIENTE</p>
        <h1>Não foi possível conectar</h1>
        <p class="description">Não conseguimos validar sua sessão agora. Confira se o servidor Laravel está funcionando e tente novamente.</p>
        <button class="primary-button" type="button" @click="reload">Tentar novamente</button>
    </main>
</template>

<script setup>
import ValtrixLogo from '../components/ValtrixLogo.vue';

function reload() {
    window.location.reload();
}
</script>

<style scoped>
.unavailable { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; text-align: center; }
.eyebrow { margin: 20px 0 6px; color: #275bd2; font-size: 14px; font-weight: 700; letter-spacing: 1.4px; text-transform: uppercase; }
h1 { margin: 0; color: #17243b; font-size: 27px; }
.description { max-width: 430px; margin: 12px 0 22px; color: #758198; font-size: 16px; line-height: 1.6; }
.primary-button { width: auto; padding: 0 24px; }
</style>
