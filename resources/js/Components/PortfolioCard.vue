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
    badge: {
        type: String,
        default: '',
    },
    tags: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <article class="group skeuo-card relative flex flex-col justify-between overflow-hidden p-4 md:p-5 transition duration-300 hover:-translate-y-1">
        <div class="absolute inset-0 -z-10 bg-black/[0.03] opacity-0 transition duration-300 group-hover:opacity-100" />

        <div>
            <div class="relative overflow-hidden rounded-2xl border border-black/25">
                <img :src="image" :alt="title" class="h-48 w-full object-cover transition duration-500 group-hover:scale-105 md:h-52">
                <span
                    v-if="badge"
                    class="absolute right-3 top-3 rounded-full border border-black/30 bg-[#ffdb67] px-3 py-1 text-xs font-bold uppercase tracking-wider text-black shadow-sm"
                >
                    {{ badge }}
                </span>
            </div>

            <div class="pt-4">
                <p class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.16em] text-black/70">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-black/30 bg-[#ffdb67]">
                        <AppIcon name="briefcase" class="h-4 w-4" />
                    </span>
                    {{ category }}
                </p>
                <h3 class="mt-2 text-2xl font-semibold leading-tight md:text-[1.75rem]">{{ title }}</h3>
                <p class="mt-2 text-base leading-relaxed text-black/80 md:text-[1.02rem]">{{ summary }}</p>
            </div>
        </div>

        <div v-if="tags && tags.length > 0" class="mt-5 flex flex-wrap gap-1.5 border-t border-black/15 pt-3">
            <span
                v-for="(tag, tIdx) in tags"
                :key="tIdx"
                class="rounded-lg border border-black/20 bg-[#ffdb67]/80 px-2.5 py-0.5 text-xs font-medium text-black/90"
            >
                {{ tag }}
            </span>
        </div>
    </article>
</template>
