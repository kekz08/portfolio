<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="skeuo-button-soft inline-flex items-center px-5 py-2.5 text-xs font-semibold uppercase tracking-[0.14em] focus:outline-none disabled:cursor-not-allowed disabled:opacity-60"
    >
        <slot />
    </button>
</template>
