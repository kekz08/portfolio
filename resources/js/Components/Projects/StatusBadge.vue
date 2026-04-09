<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    colorClass: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <span
        class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold uppercase tracking-wide shadow-[inset_0_1px_0_rgba(255,255,255,0.45)]"
        :class="colorClass"
    >
        {{ label }}
    </span>
</template>
