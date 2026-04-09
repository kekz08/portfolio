<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/Projects/StatusBadge.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    projects: {
        type: Array,
        default: () => [],
    },
});

const totalProjects = computed(() => props.projects.length);
const pendingProjects = computed(() => props.projects.filter((project) => project.status === 'pending_review').length);
const inProgressProjects = computed(() => props.projects.filter((project) => project.status === 'in_progress').length);
const completedProjects = computed(() => props.projects.filter((project) => project.status === 'completed').length);

const toDisplayDate = (value) => {
    if (!value) {
        return 'Pending';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString();
};

const toTimelineLabel = (action) => String(action || '')
    .replaceAll('_', ' ')
    .replace(/\b\w/g, (char) => char.toUpperCase());
</script>

<template>
    <Head title="User Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-black/70">
                    Project Tracker
                </p>
                <h2 class="text-[clamp(2rem,4vw,3.4rem)] font-extrabold leading-none text-black">
                    Your Dashboard
                </h2>
                <p class="max-w-3xl text-base text-black/75 sm:text-lg">
                    Track each request from intake to launch with one consistent project timeline.
                </p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-[1400px] space-y-6 px-4 sm:px-6 lg:px-8">
                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="relative overflow-hidden rounded-3xl border border-amber-700/35 bg-[#f1d368] p-6">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">Total Requests</p>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-amber-700/35 bg-amber-500/20 text-amber-800">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 7h16M4 12h16M4 17h10" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-3 text-5xl font-extrabold text-amber-800">{{ totalProjects }}</p>
                    </article>

                    <article class="relative overflow-hidden rounded-3xl border border-sky-700/30 bg-[#f1d368] p-6">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-sky-400 via-blue-500 to-indigo-500" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">Pending Review</p>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-sky-700/30 bg-sky-500/18 text-sky-800">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 8v5l3 2" />
                                    <circle cx="12" cy="12" r="8" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-3 text-5xl font-extrabold text-sky-800">{{ pendingProjects }}</p>
                    </article>

                    <article class="relative overflow-hidden rounded-3xl border border-violet-700/30 bg-[#f1d368] p-6">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-violet-400 via-purple-500 to-fuchsia-500" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">In Progress</p>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-violet-700/30 bg-violet-500/18 text-violet-800">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M7 17 17 7" />
                                    <path d="M9 7h8v8" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-3 text-5xl font-extrabold text-violet-800">{{ inProgressProjects }}</p>
                    </article>

                    <article class="relative overflow-hidden rounded-3xl border border-emerald-700/30 bg-[#f1d368] p-6">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-emerald-400 via-green-500 to-teal-500" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">Completed</p>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-emerald-700/30 bg-emerald-500/20 text-emerald-800">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m6 12 4 4 8-8" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-3 text-5xl font-extrabold text-emerald-800">{{ completedProjects }}</p>
                    </article>
                </section>

                <section
                    v-if="projects.length === 0"
                    class="rounded-3xl border border-dashed border-black/55 bg-[#f1d368] p-10 text-center"
                >
                    <span class="mx-auto mb-4 inline-flex h-14 w-14 items-center justify-center rounded-full border border-black/35 bg-[#ffdb67]">
                        <AppIcon name="briefcase" class="h-6 w-6" />
                    </span>
                    <h3 class="text-2xl font-semibold">No Project Requests Yet</h3>
                    <p class="mt-3 text-lg text-black/75">
                        Start with a new request and we will place it into your tracker automatically.
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="route('register')"
                            class="inline-flex rounded-full bg-black px-8 py-3 text-sm font-semibold uppercase tracking-[0.14em] text-[#ffdb67] transition hover:bg-black/90"
                        >
                            Submit New Request
                        </Link>
                    </div>
                </section>

                <article
                    v-for="project in projects"
                    :key="project.id"
                    class="relative overflow-hidden rounded-3xl border border-black/55 bg-[#f1d368]"
                >
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-500 to-rose-500" />
                    <div class="border-b border-black/25 p-6 sm:p-8">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="max-w-4xl">
                                <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-black/70">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-black/30 bg-[#ffdb67]">
                                        <AppIcon name="briefcase" class="h-4 w-4" />
                                    </span>
                                    Project #{{ project.id }}
                                </p>
                                <h3 class="mt-2 text-[clamp(1.8rem,3vw,2.6rem)] font-extrabold leading-tight text-black">
                                    {{ project.title }}
                                </h3>
                                <p class="mt-3 text-lg leading-relaxed text-black/80">
                                    {{ project.description || 'No description provided.' }}
                                </p>
                            </div>
                            <StatusBadge
                                :label="project.status_label"
                                :color-class="project.status_color_class"
                            />
                        </div>

                        <div class="mt-4 flex flex-wrap gap-3">
                            <p class="rounded-full border border-black/30 px-4 py-1.5 text-sm font-medium">
                                Submitted: {{ toDisplayDate(project.created_at) }}
                            </p>
                            <p
                                v-if="project.status_notes"
                                class="rounded-full border border-black/30 px-4 py-1.5 text-sm font-medium"
                            >
                                Latest Note: {{ project.status_notes }}
                            </p>
                        </div>
                    </div>

                    <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-2">
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-[0.16em] text-black/70">
                                Status Milestones
                            </h4>
                            <div class="mt-4 space-y-3">
                                <div class="rounded-2xl border border-black/30 bg-[#ffdb67] px-4 py-3 text-sm">
                                    <strong class="inline-flex items-center gap-1">
                                        <AppIcon name="calendar" class="h-4 w-4" />
                                        Submitted:
                                    </strong>
                                    {{ toDisplayDate(project.created_at) }}
                                </div>
                                <div class="rounded-2xl border border-black/30 bg-[#ffdb67] px-4 py-3 text-sm">
                                    <strong class="inline-flex items-center gap-1">
                                        <AppIcon name="check" class="h-4 w-4" />
                                        Approved:
                                    </strong>
                                    {{ toDisplayDate(project.approved_at) }}
                                </div>
                                <div class="rounded-2xl border border-black/30 bg-[#ffdb67] px-4 py-3 text-sm">
                                    <strong class="inline-flex items-center gap-1">
                                        <AppIcon name="progress" class="h-4 w-4" />
                                        In Progress:
                                    </strong>
                                    {{ toDisplayDate(project.started_at) }}
                                </div>
                                <div class="rounded-2xl border border-black/30 bg-[#ffdb67] px-4 py-3 text-sm">
                                    <strong class="inline-flex items-center gap-1">
                                        <AppIcon name="shield" class="h-4 w-4" />
                                        Completed:
                                    </strong>
                                    {{ toDisplayDate(project.completed_at) }}
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-[0.16em] text-black/70">
                                Activity Timeline
                            </h4>

                            <ul v-if="project.activity_logs?.length" class="mt-4 space-y-3">
                                <li
                                    v-for="log in project.activity_logs"
                                    :key="log.id"
                                    class="rounded-2xl border border-black/30 bg-[#ffdb67] p-4"
                                >
                                    <p class="text-sm font-semibold">
                                        <span class="mr-1 inline-flex align-middle">
                                            <AppIcon name="activity" class="h-4 w-4" />
                                        </span>
                                        {{ toTimelineLabel(log.action) }}
                                        <span v-if="log.old_status_label || log.new_status_label" class="font-normal">
                                            : {{ log.old_status_label || 'N/A' }} -> {{ log.new_status_label || 'N/A' }}
                                        </span>
                                    </p>
                                    <p v-if="log.notes" class="mt-1 text-xs text-black/70">
                                        Note: {{ log.notes }}
                                    </p>
                                    <p class="mt-1 text-xs text-black/65">
                                        By {{ log.actor || 'System' }} at {{ toDisplayDate(log.created_at) }}
                                    </p>
                                </li>
                            </ul>

                            <div
                                v-else
                                class="mt-4 rounded-2xl border border-dashed border-black/35 bg-[#ffdb67] p-4 text-sm text-black/70"
                            >
                                No activity logs yet.
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
