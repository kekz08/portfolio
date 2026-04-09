<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
        default: false,
    },
    canRegister: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <Head title="Portfolio" />

    <div class="min-h-screen bg-slate-950 text-slate-100">
        <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
            <p class="text-lg font-semibold tracking-wide">Portify Studio</p>

            <nav class="flex items-center gap-3 text-sm">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="rounded-md border border-slate-700 px-4 py-2 hover:bg-slate-800"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="rounded-md border border-slate-700 px-4 py-2 hover:bg-slate-800"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="rounded-md border border-slate-700 px-4 py-2 hover:bg-slate-800"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <main class="mx-auto grid max-w-6xl gap-12 px-6 pb-20 pt-8 md:grid-cols-2 md:items-center">
            <section>
                <p class="mb-4 inline-flex rounded-full border border-cyan-300/50 bg-cyan-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-cyan-300">
                    Laravel + Vue Portfolio System
                </p>
                <h1 class="text-4xl font-black leading-tight md:text-5xl">
                    Build Your Project With a Trackable Delivery Workflow
                </h1>
                <p class="mt-5 max-w-xl text-base text-slate-300">
                    Submit your project through our secure onboarding flow, then monitor every stage from pending review to completion in your own dashboard.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <Link
                        :href="route('hire-me')"
                        class="rounded-md bg-cyan-400 px-6 py-3 text-sm font-bold text-slate-950 transition hover:bg-cyan-300"
                    >
                        Hire Me
                    </Link>
                    <a
                        href="#workflow"
                        class="rounded-md border border-slate-700 px-6 py-3 text-sm font-semibold hover:bg-slate-800"
                    >
                        See Workflow
                    </a>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-2xl shadow-slate-950/40">
                <h2 class="text-xl font-bold">Included in your dashboard</h2>
                <ul class="mt-5 space-y-3 text-sm text-slate-300">
                    <li class="rounded-md border border-slate-800 bg-slate-950/40 p-3">
                        Pending Review: initial scope validation
                    </li>
                    <li class="rounded-md border border-slate-800 bg-slate-950/40 p-3">
                        Approved / Granted: project accepted
                    </li>
                    <li class="rounded-md border border-slate-800 bg-slate-950/40 p-3">
                        In Progress: active implementation phase
                    </li>
                    <li class="rounded-md border border-slate-800 bg-slate-950/40 p-3">
                        Completed: delivery and handover finished
                    </li>
                </ul>
            </section>
        </main>

        <section id="workflow" class="mx-auto mb-16 max-w-6xl px-6">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/40 p-6">
                <h3 class="text-xl font-bold">How it works</h3>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <article class="rounded-lg border border-slate-800 p-4">
                        <p class="text-xs font-semibold uppercase tracking-widest text-cyan-300">Step 1</p>
                        <h4 class="mt-2 text-lg font-semibold">Register</h4>
                        <p class="mt-2 text-sm text-slate-300">
                            Click <strong>Hire Me</strong>, submit your personal details and project request.
                        </p>
                    </article>
                    <article class="rounded-lg border border-slate-800 p-4">
                        <p class="text-xs font-semibold uppercase tracking-widest text-cyan-300">Step 2</p>
                        <h4 class="mt-2 text-lg font-semibold">Get Reviewed</h4>
                        <p class="mt-2 text-sm text-slate-300">
                            Our team reviews your submission and updates your status securely.
                        </p>
                    </article>
                    <article class="rounded-lg border border-slate-800 p-4">
                        <p class="text-xs font-semibold uppercase tracking-widest text-cyan-300">Step 3</p>
                        <h4 class="mt-2 text-lg font-semibold">Track Delivery</h4>
                        <p class="mt-2 text-sm text-slate-300">
                            Watch your project progress in your dashboard with timeline updates.
                        </p>
                    </article>
                </div>
            </div>
        </section>
    </div>
</template>

