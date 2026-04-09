<script setup>
import StatusBadge from '@/Components/Projects/StatusBadge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';

const props = defineProps({
    projects: {
        type: Object,
        required: true,
    },
    statusOptions: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const drafts = reactive({});

for (const project of props.projects.data) {
    drafts[project.id] = {
        status: project.status,
        status_notes: project.status_notes ?? '',
    };
}

const submit = (projectId) => {
    router.patch(
        route('admin.projects.update-status', projectId),
        {
            status: drafts[projectId].status,
            status_notes: drafts[projectId].status_notes,
        },
        {
            preserveScroll: true,
        },
    );
};

const projectItems = computed(() => props.projects.data ?? []);
const totalRequests = computed(() => Number(props.projects.total ?? projectItems.value.length));
const pendingCount = computed(() => projectItems.value.filter((project) => project.status === 'pending_review').length);
const inProgressCount = computed(() => projectItems.value.filter((project) => project.status === 'in_progress').length);
const completedCount = computed(() => projectItems.value.filter((project) => project.status === 'completed').length);

const toTimelineLabel = (value) => String(value || '')
    .replaceAll('_', ' ')
    .replace(/\b\w/g, (char) => char.toUpperCase());

const toDisplayDate = (value) => {
    if (!value) {
        return 'N/A';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString();
};
</script>

<template>
    <Head title="Project Queue" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-black/70">
                    Admin Workspace
                </p>
                <h2 class="text-[clamp(2rem,4vw,3.4rem)] font-extrabold leading-none text-black">
                    Project Queue
                </h2>
                <p class="max-w-3xl text-base text-black/75 sm:text-lg">
                    Review submissions, update status, and keep clients informed with clear activity history.
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
                                <AppIcon name="list" class="h-5 w-5" />
                            </span>
                        </div>
                        <p class="mt-3 text-5xl font-extrabold text-amber-800">{{ totalRequests }}</p>
                    </article>
                    <article class="relative overflow-hidden rounded-3xl border border-sky-700/30 bg-[#f1d368] p-6">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-sky-400 via-blue-500 to-indigo-500" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">Pending Review</p>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-sky-700/30 bg-sky-500/18 text-sky-800">
                                <AppIcon name="clock" class="h-5 w-5" />
                            </span>
                        </div>
                        <p class="mt-3 text-5xl font-extrabold text-sky-800">{{ pendingCount }}</p>
                    </article>
                    <article class="relative overflow-hidden rounded-3xl border border-violet-700/30 bg-[#f1d368] p-6">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-violet-400 via-purple-500 to-fuchsia-500" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">In Progress</p>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-violet-700/30 bg-violet-500/18 text-violet-800">
                                <AppIcon name="progress" class="h-5 w-5" />
                            </span>
                        </div>
                        <p class="mt-3 text-5xl font-extrabold text-violet-800">{{ inProgressCount }}</p>
                    </article>
                    <article class="relative overflow-hidden rounded-3xl border border-emerald-700/30 bg-[#f1d368] p-6">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-emerald-400 via-green-500 to-teal-500" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">Completed</p>
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-emerald-700/30 bg-emerald-500/20 text-emerald-800">
                                <AppIcon name="check" class="h-5 w-5" />
                            </span>
                        </div>
                        <p class="mt-3 text-5xl font-extrabold text-emerald-800">{{ completedCount }}</p>
                    </article>
                </section>

                <div
                    v-if="page.props.flash.success"
                    class="rounded-2xl border border-emerald-700/35 bg-emerald-100/70 px-4 py-3 text-sm font-medium text-emerald-900"
                >
                    {{ page.props.flash.success }}
                </div>

                <article
                    v-for="project in projectItems"
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
                                <h3 class="mt-2 text-[clamp(1.8rem,3vw,2.4rem)] font-extrabold leading-tight text-black">
                                    {{ project.title }}
                                </h3>
                                <p class="mt-2 text-lg leading-relaxed text-black/80">
                                    {{ project.description || 'No project description provided.' }}
                                </p>
                                <p class="mt-3 text-sm font-semibold text-black/85">
                                    Owner: {{ project.owner.name }} ({{ project.owner.email }})
                                </p>
                            </div>

                            <StatusBadge
                                :label="project.status_label"
                                :color-class="project.status_color_class"
                            />
                        </div>

                        <form @submit.prevent="submit(project.id)" class="mt-5 grid gap-4 lg:grid-cols-3">
                            <div>
                                <label class="mb-1 block text-sm font-semibold uppercase tracking-[0.12em] text-black/75">
                                    Status
                                </label>
                                <select
                                    v-model="drafts[project.id].status"
                                    class="block w-full rounded-2xl border border-black/35 bg-[#ffdb67] px-4 py-3 text-sm shadow-none focus:border-black focus:ring-0"
                                >
                                    <option
                                        v-for="option in statusOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <div class="lg:col-span-2">
                                <label class="mb-1 block text-sm font-semibold uppercase tracking-[0.12em] text-black/75">
                                    Status Notes
                                </label>
                                <input
                                    v-model="drafts[project.id].status_notes"
                                    type="text"
                                    class="block w-full rounded-2xl border border-black/35 bg-[#ffdb67] px-4 py-3 text-sm shadow-none focus:border-black focus:ring-0"
                                    placeholder="Add a short note for the client"
                                />
                            </div>

                            <div class="lg:col-span-3">
                                <button
                                    type="submit"
                                    class="rounded-full bg-black px-7 py-3 text-sm font-semibold uppercase tracking-[0.14em] text-[#ffdb67] transition hover:bg-black/90"
                                >
                                    Update Project Status
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="p-6 sm:p-8">
                        <h4 class="text-sm font-semibold uppercase tracking-[0.16em] text-black/70">
                            Activity Log
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
                                <p v-if="log.notes" class="mt-1 text-xs text-black/75">
                                    Note: {{ log.notes }}
                                </p>
                                <p class="mt-1 text-xs text-black/70">
                                    By {{ log.actor || 'System' }} at {{ toDisplayDate(log.created_at) }}
                                </p>
                            </li>
                        </ul>

                        <div
                            v-else
                            class="mt-4 rounded-2xl border border-dashed border-black/35 bg-[#ffdb67] px-4 py-3 text-sm text-black/70"
                        >
                            No activity logs yet.
                        </div>
                    </div>
                </article>

                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="link in projects.links"
                        :key="`${link.label}-${link.url}`"
                        :disabled="!link.url"
                        @click="link.url && router.visit(link.url)"
                        class="rounded-xl border px-4 py-2 text-sm font-semibold"
                        :class="{
                            'border-black bg-black text-[#ffdb67]': link.active,
                            'border-black/35 bg-[#f1d368] text-black hover:bg-black hover:text-[#ffdb67]': !link.active,
                            'cursor-not-allowed opacity-50': !link.url,
                        }"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
