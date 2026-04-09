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
    <Head title="Blog Details" />

    <LegacyPageRenderer
        source="/legacy/blog-details/index.html"
        page-key="legacy-blog-details"
        :route-map="routeMap"
    />
</template>
