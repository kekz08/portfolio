<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Footer from '@/Components/Footer.vue';
import Navbar from '@/Components/Navbar.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    project_title: '',
    budget: '',
    details: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form
        .transform((data) => {
            const descriptionParts = [];

            if (data.budget?.trim()) {
                descriptionParts.push(`Budget Range: ${data.budget.trim()}`);
            }

            if (data.details?.trim()) {
                descriptionParts.push(data.details.trim());
            }

            return {
                name: data.name,
                email: data.email,
                phone: data.phone,
                company: data.company,
                project_title: data.project_title,
                project_description: descriptionParts.join('\n\n'),
                password: data.password,
                password_confirmation: data.password_confirmation,
            };
        })
        .post(route('register'), {
            onFinish: () => {
                form.transform((data) => data);
                form.reset('password', 'password_confirmation');
            },
        });
};
</script>

<template>
    <Head title="Hire Me" />

    <div class="skeuo-page flex min-h-screen flex-col bg-[#ffdb67] text-black">
        <Navbar :scrolled="true" :mobile-menu-open="false" :show-mobile-toggle="false" />

        <main class="mx-auto max-w-[1150px] flex-1 px-6 pb-20 pt-36 sm:px-10">
            <div class="mb-10">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-black/70">Hire Me / Registration</p>
                <h1 class="mt-2 text-[clamp(2.6rem,6vw,5.5rem)] font-extrabold leading-none">Start Your Project Request</h1>
                <p class="mt-4 max-w-4xl text-[clamp(1.1rem,2vw,1.55rem)] leading-relaxed">
                    Complete this form to create your account and submit your project.
                    After successful registration, you will be redirected to your dashboard.
                </p>
            </div>

            <form class="relative space-y-5 overflow-hidden rounded-3xl border border-black/45 bg-[#f1d368] p-8 sm:p-10" @submit.prevent="submit">
                <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500" />
                <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-black/35 bg-[#ffdb67] px-3 py-1.5 text-sm font-semibold uppercase tracking-[0.12em]">
                    <AppIcon name="sparkles" class="h-4 w-4" />
                    Client Onboarding
                </div>
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Full Name</span>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            autocomplete="name"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-3 text-lg focus:border-black focus:outline-none focus:ring-0"
                        >
                        <p v-if="form.errors.name" class="text-sm font-medium text-red-700">{{ form.errors.name }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Email</span>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            autocomplete="username"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-3 text-lg focus:border-black focus:outline-none focus:ring-0"
                        >
                        <p v-if="form.errors.email" class="text-sm font-medium text-red-700">{{ form.errors.email }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Phone</span>
                        <input
                            v-model="form.phone"
                            type="text"
                            autocomplete="tel"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-3 text-lg focus:border-black focus:outline-none focus:ring-0"
                        >
                        <p v-if="form.errors.phone" class="text-sm font-medium text-red-700">{{ form.errors.phone }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Company</span>
                        <input
                            v-model="form.company"
                            type="text"
                            autocomplete="organization"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-3 text-lg focus:border-black focus:outline-none focus:ring-0"
                        >
                        <p v-if="form.errors.company" class="text-sm font-medium text-red-700">{{ form.errors.company }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Project Title</span>
                        <input
                            v-model="form.project_title"
                            type="text"
                            required
                            maxlength="255"
                            placeholder="Portfolio Redesign"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-3 text-lg focus:border-black focus:outline-none focus:ring-0"
                        >
                        <p v-if="form.errors.project_title" class="text-sm font-medium text-red-700">{{ form.errors.project_title }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Budget Range</span>
                        <input
                            v-model="form.budget"
                            type="text"
                            placeholder="$1,000 - $5,000"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-3 text-lg focus:border-black focus:outline-none focus:ring-0"
                        >
                    </label>
                </div>

                <label class="block space-y-2">
                    <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Project Details</span>
                    <textarea
                        v-model="form.details"
                        rows="6"
                        placeholder="Describe your goals, timeline, and required features."
                        class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-3 text-lg focus:border-black focus:outline-none focus:ring-0"
                    />
                    <p v-if="form.errors.project_description" class="text-sm font-medium text-red-700">{{ form.errors.project_description }}</p>
                </label>

                <div class="grid gap-5 md:grid-cols-2">
                    <label class="space-y-2">
                        <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Password</span>
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-3 text-lg focus:border-black focus:outline-none focus:ring-0"
                        >
                        <p v-if="form.errors.password" class="text-sm font-medium text-red-700">{{ form.errors.password }}</p>
                    </label>

                    <label class="space-y-2">
                        <span class="text-sm font-semibold uppercase tracking-[0.13em] text-black/70">Confirm Password</span>
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-3 text-lg focus:border-black focus:outline-none focus:ring-0"
                        >
                    </label>
                </div>

                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-full bg-black px-8 py-3 text-sm font-semibold uppercase tracking-[0.14em] text-[#ffdb67] transition hover:bg-black/90 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ form.processing ? 'Submitting...' : 'Create Account & Submit' }}
                    </button>
                    <Link
                        :href="route('home')"
                        class="rounded-full border border-black px-8 py-3 text-sm font-semibold uppercase tracking-[0.14em] transition hover:bg-black hover:text-[#ffdb67]"
                    >
                        Back to Home
                    </Link>
                </div>
            </form>
        </main>

        <Footer />
    </div>
</template>
