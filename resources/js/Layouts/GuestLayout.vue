<script setup>
import Footer from '@/Components/Footer.vue';
import Navbar from '@/Components/Navbar.vue';
</script>

<template>
    <div class="skeuo-page flex min-h-screen flex-col text-black">
        <Navbar :scrolled="true" :mobile-menu-open="false" :show-mobile-toggle="false" />

        <main class="mx-auto max-w-[980px] flex-1 px-6 pb-20 pt-36 sm:px-10">
            <section class="skeuo-card p-8 sm:p-10">
                <slot />
            </section>
        </main>

        <Footer />
    </div>
</template>
