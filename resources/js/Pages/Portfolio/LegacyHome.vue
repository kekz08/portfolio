<script setup>
import LegacyPageRenderer from '@/Components/Legacy/LegacyPageRenderer.vue';
import { Head } from '@inertiajs/vue3';

const routeMap = {
    '': '/',
    'index.html': '/',
    'blogs': '/blogs',
    'blogs/index.html': '/blogs',
    'blog-details': '/blog-details',
    'blog-details/index.html': '/blog-details',
};
</script>

<template>
    <Head title="Portfolio" />

    <LegacyPageRenderer
        source="/legacy/index.html"
        page-key="legacy-home"
        :route-map="routeMap"
        force-hire-me-cta
    />
</template>
