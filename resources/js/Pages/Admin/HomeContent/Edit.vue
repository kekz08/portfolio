<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const localError = ref('');

const serialize = (value) => JSON.stringify(value, null, 2);

const form = useForm({
    content: props.content,
});

const contentJson = ref(serialize(props.content));

watch(
    () => props.content,
    (value) => {
        form.content = value;
        contentJson.value = serialize(value);
    },
    { deep: true },
);

const syncJsonToForm = () => {
    localError.value = '';

    try {
        const parsed = JSON.parse(contentJson.value);
        form.content = parsed;
        contentJson.value = serialize(parsed);
        return true;
    } catch (error) {
        localError.value = `Invalid JSON: ${error?.message ?? 'Unable to parse content.'}`;
        return false;
    }
};

const submit = () => {
    if (!syncJsonToForm()) {
        return;
    }

    form.patch(route('admin.home-content.update'), {
        preserveScroll: true,
        onSuccess: () => {
            contentJson.value = serialize(form.content);
        },
    });
};

const resetDefaults = () => {
    router.post(route('admin.home-content.reset'), {}, { preserveScroll: true });
};
</script>

<template>
    <Head title="Admin Home Content" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-black/70">
                    Admin Workspace
                </p>
                <h2 class="text-[clamp(1.75rem,3vw,2.85rem)] font-extrabold leading-none text-black">
                    Home Content
                </h2>
                <p class="text-sm text-black/75 sm:text-base">
                    Manage all data displayed on the portfolio home page from one backend source.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-[1320px] space-y-4 px-4 sm:px-6 lg:px-8">
                <div
                    v-if="page.props.flash.success"
                    class="rounded-2xl border border-emerald-700/35 bg-emerald-100/70 px-4 py-3 text-sm font-medium text-emerald-900"
                >
                    {{ page.props.flash.success }}
                </div>

                <div
                    v-if="localError"
                    class="rounded-2xl border border-rose-700/35 bg-rose-100/70 px-4 py-3 text-sm font-medium text-rose-900"
                >
                    {{ localError }}
                </div>

                <div
                    v-if="Object.keys(form.errors).length > 0"
                    class="rounded-2xl border border-rose-700/35 bg-rose-100/70 px-4 py-3 text-sm text-rose-900"
                >
                    <p class="font-semibold">
                        Unable to save. Please review the JSON shape.
                    </p>
                    <p
                        v-for="(error, key) in form.errors"
                        :key="key"
                        class="mt-1"
                    >
                        {{ key }}: {{ error }}
                    </p>
                </div>

                <section class="skeuo-card p-5 sm:p-6">
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-black/35 bg-[#ffdb67] px-3 py-1 text-xs font-semibold uppercase tracking-[0.12em]">
                        <AppIcon name="code" class="h-4 w-4" />
                        Content JSON Editor
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-xl font-extrabold text-black sm:text-2xl">
                                Home Page Payload
                            </h3>
                            <p class="text-sm text-black/75">
                                This JSON controls sections, text, cards, contact info, and everything rendered in
                                <code>Home.vue</code>.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="skeuo-button-soft px-4 py-2 text-xs font-semibold sm:text-sm"
                                @click="syncJsonToForm"
                            >
                                Format JSON
                            </button>
                            <button
                                type="button"
                                class="skeuo-button-soft px-4 py-2 text-xs font-semibold sm:text-sm"
                                @click="resetDefaults"
                            >
                                Reset to Defaults
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="mt-4 space-y-4">
                        <textarea
                            v-model="contentJson"
                            rows="36"
                            spellcheck="false"
                            class="w-full rounded-2xl border border-black/35 bg-[#ffdb67] p-3.5 font-mono text-xs text-black shadow-none focus:border-black focus:ring-0 sm:text-sm"
                        />

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="skeuo-button px-5 py-2 text-sm font-semibold disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                {{ form.processing ? 'Saving...' : 'Save Home Content' }}
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
