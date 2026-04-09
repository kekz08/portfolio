<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-2xl border border-black/35 bg-[#ffdb67] px-4 py-2.5 text-base shadow-none focus:border-black focus:ring-0"
        v-model="model"
        ref="input"
    />
</template>
