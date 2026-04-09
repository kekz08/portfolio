<script setup>
import AppIcon from '@/Components/UI/AppIcon.vue';

defineProps({
    image: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    category: {
        type: String,
        required: true,
    },
    summary: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <article class="group skeuo-card relative overflow-hidden p-5 transition duration-300 hover:-translate-y-1">
        <div class="absolute inset-0 -z-10 bg-black/[0.03] opacity-0 transition duration-300 group-hover:opacity-100" />

        <div class="overflow-hidden rounded-2xl border border-black/25">
            <img
                :src="image"
                :alt="title"
                class="h-56 w-full object-cover transition duration-500 group-hover:scale-105"
            >
        </div>

        <div class="pt-5">
            <p class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.16em] text-black/70">
                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-black/30 bg-[#ffdb67]">
                    <AppIcon name="briefcase" class="h-4 w-4" />
                </span>
                {{ category }}
            </p>
            <h3 class="mt-2 text-3xl font-semibold leading-tight">{{ title }}</h3>
            <p class="mt-3 text-lg leading-relaxed text-black/80">{{ summary }}</p>
        </div>
    </article>
</template>
