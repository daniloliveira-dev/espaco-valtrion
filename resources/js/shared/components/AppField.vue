<template>
    <div class="field">
        <label class="field-label" :for="id">{{ label }}</label>
        <div class="field-control">
            <input
                :id="id"
                v-bind="$attrs"
                class="field-input"
                :type="type"
                :placeholder="placeholder"
                :autocomplete="autocomplete"
                :aria-invalid="Boolean(error)"
            >
        </div>
        <p v-if="error" class="field-error" role="alert">{{ error }}</p>
    </div>
</template>

<script setup>
defineOptions({ inheritAttrs: false });

defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    autocomplete: { type: String, default: 'off' },
    error: { type: String, default: '' },
});
</script>
