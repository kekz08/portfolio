<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block text-sm font-semibold uppercase tracking-[0.12em] text-black/75">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
