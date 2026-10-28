<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/Projects/StatusBadge.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue';

const props = defineProps({
    projects: {
        type: Array,
        default: () => [],
    },
});
const page = usePage();

const liveProjects = ref([...props.projects]);
const subscriptions = new Map();
const typingIndicators = reactive({});
const typingSendTimers = new Map();
const typingClearTimers = new Map();
const typingClientKey = `dash-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;

const totalProjects = computed(() => liveProjects.value.length);
const pendingProjects = computed(() => liveProjects.value.filter((project) => ['pending', 'pending_review'].includes(project.status)).length);
const activeProjects = computed(() => liveProjects.value.filter((project) => ['approved', 'in_progress'].includes(project.status)).length);
const completedProjects = computed(() => liveProjects.value.filter((project) => project.status === 'completed').length);
const messageDrafts = reactive({});

const currentUser = computed(() => page.props.auth?.user ?? null);

const syncProjects = (projects) => {
    liveProjects.value = [...projects];
};

const applyProjectUpdate = (payload) => {
    const project = liveProjects.value.find((item) => item.id === payload.project_id);

    if (!project) {
        return;
    }

    if (payload.status) {
        project.status = payload.status;
        project.status_label = payload.status_label ?? project.status_label;
        project.status_color_class = payload.status_color_class ?? project.status_color_class;
        project.status_notes = payload.status_notes ?? project.status_notes;
        project.can_collaborate = ['approved', 'in_progress', 'completed'].includes(project.status);
    }

    if (typeof payload.progress_percentage === 'number') {
        project.progress_percentage = payload.progress_percentage;
    }

    if (payload.start_date) {
        project.start_date = payload.start_date;
    }

    if (payload.completed_at) {
        project.completed_at = payload.completed_at;
    }

    if (payload.update) {
        if (!Array.isArray(project.updates)) {
            project.updates = [];
        }
        const exists = project.updates.some((update) => update.id === payload.update.id);
        if (!exists) {
            project.updates.unshift(payload.update);
        }
    }

    if (payload.activity) {
        if (!Array.isArray(project.activity_logs)) {
            project.activity_logs = [];
        }
        const exists = project.activity_logs.some((log) => log.id === payload.activity.id);
        if (!exists) {
            project.activity_logs.unshift(payload.activity);
        }
    }
};

const applyProjectMessage = (payload) => {
    const project = liveProjects.value.find((item) => item.id === payload.project_id);

    if (!project || !payload.message) {
        return;
    }

    if (!Array.isArray(project.messages)) {
        project.messages = [];
    }

    const exists = project.messages.some((message) => message.id === payload.message.id);
    if (!exists) {
        project.messages.unshift(payload.message);
    }

    if (payload.activity) {
        if (!Array.isArray(project.activity_logs)) {
            project.activity_logs = [];
        }
        const existsActivity = project.activity_logs.some((log) => log.id === payload.activity.id);
        if (!existsActivity) {
            project.activity_logs.unshift(payload.activity);
        }
    }
};

const channelName = (projectId) => `project.${projectId}`;

const handleTyping = (projectId, payload) => {
    if (!payload?.client_key || payload.client_key === typingClientKey) {
        return;
    }

    typingIndicators[projectId] = payload.name || 'User';

    if (typingClearTimers.has(projectId)) {
        clearTimeout(typingClearTimers.get(projectId));
    }

    typingClearTimers.set(projectId, setTimeout(() => {
        delete typingIndicators[projectId];
        typingClearTimers.delete(projectId);
    }, 2500));
};

const emitTyping = (projectId) => {
    if (!window.Echo || !currentUser.value) {
        return;
    }

    window.Echo.private(channelName(projectId)).whisper('typing', {
        client_key: typingClientKey,
        name: currentUser.value.name,
    });
};

const scheduleTyping = (projectId) => {
    if (typingSendTimers.has(projectId)) {
        return;
    }

    emitTyping(projectId);
    typingSendTimers.set(projectId, setTimeout(() => {
        typingSendTimers.delete(projectId);
    }, 1200));
};

const subscribeToProject = (projectId) => {
    if (!window.Echo || subscriptions.has(projectId)) {
        return;
    }

    const channel = window.Echo.private(channelName(projectId))
        .listen('.project.updated', applyProjectUpdate)
        .listen('.project.message.sent', applyProjectMessage)
        .listenForWhisper('typing', (payload) => handleTyping(projectId, payload));

    subscriptions.set(projectId, channel);
};

const syncSubscriptions = () => {
    const ids = new Set(liveProjects.value.map((project) => project.id));

    for (const [projectId, channel] of subscriptions.entries()) {
        if (!ids.has(projectId)) {
            if (window.Echo) {
                window.Echo.leave(channelName(projectId));
            } else {
                channel.stopListening('.project.updated');
                channel.stopListening('.project.message.sent');
            }
            subscriptions.delete(projectId);
        }
    }

    ids.forEach((projectId) => subscribeToProject(projectId));
};

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

const normalizedProgress = (value) => {
    const parsed = Number(value);

    if (!Number.isFinite(parsed)) {
        return 0;
    }

    return Math.max(0, Math.min(100, parsed));
};

const formatProjectRef = (project) => {
    if (project?.reference_code) {
        return project.reference_code;
    }
    const raw = String(project?.id ?? '');
    if (/^\d+$/.test(raw)) {
        return `PRJ-${raw.padStart(4, '0')}`;
    }
    if (raw.startsWith('PRJ-')) {
        return raw;
    }
    return 'PRJ-REQUEST';
};

const hasError = (key) => Boolean(page.props.errors?.[key]);

const sendMessage = (projectId) => {
    const message = String(messageDrafts[projectId] ?? '').trim();

    if (!message) {
        return;
    }

    router.post(route('projects.messages.store', projectId), { message }, {
        preserveScroll: true,
        onSuccess: () => {
            messageDrafts[projectId] = '';
        },
    });
};

watch(
    () => props.projects,
    (value) => {
        syncProjects(value);
        syncSubscriptions();
    },
    { deep: true },
);

onMounted(() => {
    syncSubscriptions();
});

onBeforeUnmount(() => {
    for (const [projectId, channel] of subscriptions.entries()) {
        if (window.Echo) {
            window.Echo.leave(channelName(projectId));
            continue;
        }

        channel.stopListening('.project.updated');
        channel.stopListening('.project.message.sent');
    }
    subscriptions.clear();

    for (const timer of typingSendTimers.values()) {
        clearTimeout(timer);
    }
    typingSendTimers.clear();

    for (const timer of typingClearTimers.values()) {
        clearTimeout(timer);
    }
    typingClearTimers.clear();
});
</script>

<template>
    <Head title="User Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-black/70">
                    Project Tracker
                </p>
                <h2 class="text-[clamp(1.75rem,3vw,2.85rem)] font-extrabold leading-none text-black">
                    Your Dashboard
                </h2>
                <p class="max-w-3xl text-sm text-black/75 sm:text-base">
                    Track each request from intake to launch with one consistent project timeline.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-[1320px] space-y-5 px-4 sm:px-6 lg:px-8">
                <div
                    v-if="page.props.flash.success"
                    class="rounded-2xl border border-emerald-700/35 bg-emerald-100/70 px-4 py-3 text-sm font-medium text-emerald-900"
                >
                    {{ page.props.flash.success }}
                </div>
                <div
                    v-if="page.props.flash.error"
                    class="rounded-2xl border border-rose-700/35 bg-rose-100/70 px-4 py-3 text-sm font-medium text-rose-900"
                >
                    {{ page.props.flash.error }}
                </div>

                <div
                    v-if="Object.keys(page.props.errors || {}).length"
                    class="rounded-2xl border border-rose-700/35 bg-rose-100/70 px-4 py-3 text-sm font-medium text-rose-900"
                >
                    Some fields need attention. Please review the highlighted form fields and try again.
                </div>

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="relative overflow-hidden rounded-3xl border border-amber-700/35 bg-[#f1d368] p-5">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">Total Requests</p>
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-amber-700/35 bg-amber-500/20 text-amber-800">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 7h16M4 12h16M4 17h10" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-2 text-4xl font-extrabold text-amber-800">{{ totalProjects }}</p>
                    </article>

                    <article class="relative overflow-hidden rounded-3xl border border-sky-700/30 bg-[#f1d368] p-5">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-sky-400 via-blue-500 to-indigo-500" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">Pending Review</p>
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-sky-700/30 bg-sky-500/18 text-sky-800">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 8v5l3 2" />
                                    <circle cx="12" cy="12" r="8" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-2 text-4xl font-extrabold text-sky-800">{{ pendingProjects }}</p>
                    </article>

                    <article class="relative overflow-hidden rounded-3xl border border-violet-700/30 bg-[#f1d368] p-5">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-violet-400 via-purple-500 to-fuchsia-500" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">Active Projects</p>
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-violet-700/30 bg-violet-500/18 text-violet-800">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M7 17 17 7" />
                                    <path d="M9 7h8v8" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-2 text-4xl font-extrabold text-violet-800">{{ activeProjects }}</p>
                    </article>

                    <article class="relative overflow-hidden rounded-3xl border border-emerald-700/30 bg-[#f1d368] p-5">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-emerald-400 via-green-500 to-teal-500" />
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/70">Completed</p>
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-emerald-700/30 bg-emerald-500/20 text-emerald-800">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m6 12 4 4 8-8" />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-2 text-4xl font-extrabold text-emerald-800">{{ completedProjects }}</p>
                    </article>
                </section>

                <section
                    v-if="liveProjects.length === 0"
                    class="rounded-3xl border border-dashed border-black/55 bg-[#f1d368] p-7 text-center"
                >
                    <span class="mx-auto mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full border border-black/35 bg-[#ffdb67]">
                        <AppIcon name="briefcase" class="h-6 w-6" />
                    </span>
                    <h3 class="text-xl font-semibold">No Project Requests Yet</h3>
                    <p class="mt-2 text-base text-black/75">
                        Start with a new request and we will place it into your tracker automatically.
                    </p>
                    <div class="mt-5">
                        <Link
                            :href="route('register')"
                            class="inline-flex rounded-full bg-black px-7 py-2.5 text-sm font-semibold uppercase tracking-[0.14em] text-[#ffdb67] transition hover:bg-black/90"
                        >
                            Submit New Request
                        </Link>
                    </div>
                </section>

                <article
                    v-for="project in liveProjects"
                    :key="project.id"
                    class="relative overflow-hidden rounded-3xl border border-black/55 bg-[#f1d368]"
                >
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-500 to-rose-500" />
                    <div class="border-b border-black/25 p-5 sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="max-w-4xl">
                                <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-black/70">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-black/30 bg-[#ffdb67]">
                                        <AppIcon name="briefcase" class="h-4 w-4" />
                                    </span>
                                    Project #{{ formatProjectRef(project) }}
                                </p>
                                <h3 class="mt-2 text-[clamp(1.5rem,2.4vw,2.15rem)] font-extrabold leading-tight text-black">
                                    {{ project.title }}
                                </h3>
                                <p class="mt-2 text-base leading-relaxed text-black/80">
                                    {{ project.description || 'No description provided.' }}
                                </p>

                                <div class="mt-3 grid gap-2 text-sm font-medium text-black/85 sm:grid-cols-2">
                                    <p class="rounded-full border border-black/30 px-3 py-1.5">
                                        Budget: {{ project.budget || 'Not provided' }}
                                    </p>
                                    <p class="rounded-full border border-black/30 px-3 py-1.5">
                                        Deadline: {{ project.deadline ? toDisplayDate(project.deadline) : 'Not set' }}
                                    </p>
                                </div>

                                <div class="mt-3">
                                    <div class="mb-1 flex items-center justify-between text-xs font-semibold uppercase tracking-[0.14em] text-black/70">
                                        <span>Overall Progress</span>
                                        <span>{{ normalizedProgress(project.progress_percentage) }}%</span>
                                    </div>
                                    <div class="h-2.5 overflow-hidden rounded-full border border-black/35 bg-[#ffdb67]">
                                        <div
                                            class="h-full bg-black transition-all duration-500"
                                            :style="{ width: `${normalizedProgress(project.progress_percentage)}%` }"
                                        />
                                    </div>
                                </div>
                            </div>
                            <StatusBadge
                                :label="project.status_label"
                                :color-class="project.status_color_class"
                            />
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2.5">
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

                    <div class="grid gap-5 p-5 sm:p-6 lg:grid-cols-2">
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-[0.16em] text-black/70">
                                Status Milestones
                            </h4>
                            <div class="mt-3 space-y-2.5">
                                <div class="rounded-2xl border border-black/30 bg-[#ffdb67] px-4 py-2.5 text-sm">
                                    <strong class="inline-flex items-center gap-1">
                                        <AppIcon name="calendar" class="h-4 w-4" />
                                        Submitted:
                                    </strong>
                                    {{ toDisplayDate(project.created_at) }}
                                </div>
                                <div class="rounded-2xl border border-black/30 bg-[#ffdb67] px-4 py-2.5 text-sm">
                                    <strong class="inline-flex items-center gap-1">
                                        <AppIcon name="check" class="h-4 w-4" />
                                        Approved:
                                    </strong>
                                    {{ toDisplayDate(project.approved_at) }}
                                </div>
                                <div class="rounded-2xl border border-black/30 bg-[#ffdb67] px-4 py-2.5 text-sm">
                                    <strong class="inline-flex items-center gap-1">
                                        <AppIcon name="progress" class="h-4 w-4" />
                                        In Progress:
                                    </strong>
                                    {{ toDisplayDate(project.start_date) }}
                                </div>
                                <div class="rounded-2xl border border-black/30 bg-[#ffdb67] px-4 py-2.5 text-sm">
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

                            <ul v-if="project.activity_logs?.length" class="mt-3 space-y-2.5">
                                <li
                                    v-for="log in project.activity_logs"
                                    :key="log.id"
                                    class="rounded-2xl border border-black/30 bg-[#ffdb67] p-3.5"
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
                                class="mt-3 rounded-2xl border border-dashed border-black/35 bg-[#ffdb67] p-3.5 text-sm text-black/70"
                            >
                                No activity logs yet.
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-5 border-t border-black/20 p-5 sm:p-6 lg:grid-cols-2">
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-[0.16em] text-black/70">
                                Progress Updates
                            </h4>

                            <transition-group
                                v-if="project.updates?.length"
                                name="fade-slide"
                                tag="ul"
                                class="mt-3 space-y-2.5"
                            >
                                <li
                                    v-for="update in project.updates"
                                    :key="update.id"
                                    class="chat-pop rounded-2xl border border-black/30 bg-[#ffdb67] p-3.5"
                                >
                                    <p class="text-sm font-semibold">
                                        {{ update.title }}
                                        <span class="ml-2 text-xs font-medium text-black/65">
                                            {{ update.progress_percentage }}%
                                        </span>
                                    </p>
                                    <p class="mt-1 text-sm text-black/75">{{ update.description }}</p>
                                    <p class="mt-1 text-xs text-black/65">
                                        By {{ update.created_by || 'Admin' }} at {{ toDisplayDate(update.created_at) }}
                                    </p>
                                </li>
                            </transition-group>

                            <div
                                v-else
                                class="mt-3 rounded-2xl border border-dashed border-black/35 bg-[#ffdb67] p-3.5 text-sm text-black/70"
                            >
                                No progress updates yet.
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-[0.16em] text-black/70">
                                Project Chat
                            </h4>

                            <transition-group
                                v-if="project.messages?.length"
                                name="fade-slide"
                                tag="ul"
                                class="mt-3 max-h-64 space-y-2 overflow-y-auto pr-1"
                            >
                                <li
                                    v-for="message in project.messages"
                                    :key="message.id"
                                    class="chat-pop rounded-2xl border border-black/30 bg-[#ffdb67] p-3"
                                >
                                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-black/70">
                                        {{ message.sender_name || 'User' }}
                                        <span class="font-normal text-black/60">({{ message.sender_role || 'client' }})</span>
                                    </p>
                                    <p class="mt-1 text-sm leading-relaxed">{{ message.message }}</p>
                                    <p class="mt-1 text-xs text-black/60">{{ toDisplayDate(message.created_at) }}</p>
                                </li>
                            </transition-group>

                            <div
                                v-else
                                class="mt-3 rounded-2xl border border-dashed border-black/35 bg-[#ffdb67] p-3.5 text-sm text-black/70"
                            >
                                No messages yet.
                            </div>

                            <div
                                v-if="typingIndicators[project.id]"
                                class="mt-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.12em] text-black/60"
                            >
                                <span>{{ typingIndicators[project.id] }} typing</span>
                                <span class="flex items-center gap-1">
                                    <span class="typing-dot" />
                                    <span class="typing-dot" />
                                    <span class="typing-dot" />
                                </span>
                            </div>

                            <form
                                v-if="project.can_collaborate"
                                class="mt-3 space-y-2"
                                @submit.prevent="sendMessage(project.id)"
                            >
                                <textarea
                                    v-model="messageDrafts[project.id]"
                                    @input="scheduleTyping(project.id)"
                                    rows="3"
                                    class="w-full rounded-2xl border px-3 py-2 text-sm shadow-none focus:ring-0"
                                    :class="hasError('message') ? 'border-rose-500 bg-rose-50/70 focus:border-rose-600' : 'border-black/35 bg-[#ffdb67] focus:border-black'"
                                    placeholder="Write a message to the project admin..."
                                />
                                <p v-if="hasError('message')" class="text-xs font-semibold text-rose-700">
                                    {{ page.props.errors.message }}
                                </p>
                                <button
                                    type="submit"
                                    class="rounded-full border border-black px-5 py-2 text-xs font-semibold uppercase tracking-[0.14em] transition hover:bg-black hover:text-[#ffdb67] sm:text-sm"
                                >
                                    Send Message
                                </button>
                            </form>

                            <p
                                v-else
                                class="mt-3 rounded-2xl border border-dashed border-black/35 bg-[#ffdb67] p-3.5 text-xs text-black/70"
                            >
                                Chat will unlock after your project is approved.
                            </p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
