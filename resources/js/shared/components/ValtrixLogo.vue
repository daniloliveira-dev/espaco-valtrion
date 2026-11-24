<template>
    <a class="valtrix-brand" :href="href" aria-label="Valtrix">
        <svg class="valtrix-mark" viewBox="0 0 100 100" role="img" aria-label="Símbolo Valtrix">
            <defs>
                <linearGradient :id="gradientId" x1="13" y1="27" x2="84" y2="72" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#40d5a5" />
                    <stop offset=".52" stop-color="#2780ce" />
                    <stop offset="1" stop-color="#183ee8" />
                </linearGradient>
            </defs>
            <g fill="none" :stroke="`url(#${gradientId})`" stroke-linecap="round" stroke-linejoin="round" stroke-width="7">
                <path d="M50 10 79 27v34L50 78 21 61V27L50 10Z" />
                <path d="m50 24 18 11v19M32 37v20l18 11m-18-31 36 17M50 24l18 11 11-8" />
            </g>
            <g :fill="`url(#${gradientId})`">
                <circle cx="50" cy="10" r="6" />
                <circle cx="79" cy="27" r="6" />
                <circle cx="79" cy="61" r="6" />
                <circle cx="50" cy="78" r="6" />
                <circle cx="21" cy="61" r="6" />
                <circle cx="21" cy="27" r="6" />
                <circle cx="50" cy="24" r="5.5" />
                <circle cx="32" cy="37" r="5.5" />
                <circle cx="68" cy="54" r="5.5" />
                <circle cx="50" cy="65" r="5.5" />
            </g>
        </svg>
        <span class="valtrix-wordmark">Valtrix</span>
    </a>
</template>

<script setup>
import { useId } from 'vue';

const gradientId = `valtrix-gradient-${useId()}`;

defineProps({
    href: { type: String, default: '/dashboard' },
});
</script>
