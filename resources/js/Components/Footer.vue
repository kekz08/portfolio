<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const props = defineProps({
    brand: {
        type: String,
        default: '',
    },
    tagline: {
        type: String,
        default: '',
    },
});

const sharedFooter = computed(() => page.props.site?.footer ?? {});
const resolvedBrand = computed(() => props.brand || sharedFooter.value.brand || 'Portify');
const resolvedTagline = computed(() => props.tagline || sharedFooter.value.tagline || 'Designed with Vue + Tailwind');
</script>

<template>
    <footer class="mt-auto border-t border-black/30 py-8 skeuo-page">
        <div class="mx-auto flex max-w-[1900px] flex-wrap items-center justify-center gap-3 px-6 text-center text-sm sm:px-10 lg:px-16">
            <p>Copyright {{ new Date().getFullYear() }} {{ resolvedBrand }}</p>
            <p>|</p>
            <p>{{ resolvedTagline }}</p>
        </div>
    </footer>
</template>
