<script setup>
import Footer from '@/Components/Footer.vue';
import Navbar from '@/Components/Navbar.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="skeuo-page flex min-h-screen flex-col bg-[#ffdb67] text-black">
        <Navbar :scrolled="true" :mobile-menu-open="false" :show-mobile-toggle="false" />

        <main class="mx-auto max-w-[1020px] flex-1 px-6 pb-16 pt-32 sm:px-10">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-black/70">Account Access</p>
                <h1 class="mt-2 text-[clamp(2rem,4.5vw,3.9rem)] font-extrabold leading-[0.95]">Welcome Back</h1>
                <p class="mt-3 max-w-3xl text-[clamp(1rem,1.25vw,1.15rem)] leading-relaxed">
                    Log in to manage your project requests, see status updates, and continue collaboration.
                </p>
            </div>

            <section class="relative overflow-hidden rounded-3xl border border-black/45 bg-[#f1d368] p-6 sm:p-7">
                <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500" />
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-black/35 bg-[#ffdb67] px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">
                    <AppIcon name="lock" class="h-4 w-4" />
                    Secure Login
                </div>
                <div
                    v-if="status"
                    class="mb-4 rounded-2xl border border-emerald-700/35 bg-emerald-100/70 px-4 py-2.5 text-sm font-medium text-emerald-900"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <label class="block space-y-1.5">
                        <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Email</span>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-2.5 text-base focus:border-black focus:outline-none focus:ring-0"
                        >
                        <p v-if="form.errors.email" class="text-sm font-medium text-red-700">{{ form.errors.email }}</p>
                    </label>

                    <label class="block space-y-1.5">
                        <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Password</span>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-2.5 text-base focus:border-black focus:outline-none focus:ring-0"
                        >
                        <p v-if="form.errors.password" class="text-sm font-medium text-red-700">{{ form.errors.password }}</p>
                    </label>

                    <label class="inline-flex items-center gap-3 text-sm font-medium">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            class="h-4 w-4 rounded border-black/50 bg-[#ffdb67] text-black focus:ring-black"
                        >
                        <span>Remember me</span>
                    </label>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm font-semibold underline underline-offset-2 hover:text-black/70"
                        >
                            Forgot your password?
                        </Link>
                        <div v-else />

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-full bg-black px-7 py-2.5 text-xs font-semibold uppercase tracking-[0.14em] text-[#ffdb67] transition hover:bg-black/90 disabled:cursor-not-allowed disabled:opacity-60 sm:text-sm"
                        >
                            {{ form.processing ? 'Signing In...' : 'Log In' }}
                        </button>
                    </div>
                </form>
            </section>
        </main>

        <Footer />
    </div>
</template>
