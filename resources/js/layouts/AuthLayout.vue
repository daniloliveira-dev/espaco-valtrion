<template>
    <main class="auth-shell">
        <aside class="auth-aside">
            <ValtrixLogo href="/login" />

            <div class="aside-copy">
                <span class="eyebrow"><span aria-hidden="true">✦</span> Tecnologia que conecta</span>
                <h1>Inteligência<br>em movimento.</h1>
                <p>Uma plataforma conectada para organizar o cuidado, simplificar rotinas e aproximar sua equipe das pessoas.</p>
            </div>

            <div class="trust-row">
                <div class="trust-avatars" aria-hidden="true">
                    <span>MA</span><span>RC</span><span>PL</span><span>+</span>
                </div>
                <span>Conectando pessoas. Transformando cuidado.</span>
            </div>
        </aside>

        <section class="auth-main">
            <div class="auth-card">
                <ValtrixLogo class="mobile-brand" href="/login" />
                <RouterView />
                <p class="auth-footer">© {{ new Date().getFullYear() }} Valtrix · Plataforma demonstrativa</p>
            </div>
        </section>
    </main>
</template>

<script setup>
import { RouterView } from 'vue-router';
import ValtrixLogo from '../shared/components/ValtrixLogo.vue';
</script>
