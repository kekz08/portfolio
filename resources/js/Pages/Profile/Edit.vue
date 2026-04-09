<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2">
                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-black/70">
                    Account Settings
                </p>
                <h2 class="text-[clamp(2rem,4vw,3.2rem)] font-extrabold leading-none text-black">
                    Profile
                </h2>
                <p class="max-w-3xl text-base text-black/75 sm:text-lg">
                    Manage your personal details, password, and account security in one place.
                </p>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-[1400px] space-y-6 px-4 sm:px-6 lg:px-8">
                <div
                    class="relative overflow-hidden rounded-3xl border border-black/55 bg-[#f1d368] p-6 sm:p-8"
                >
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-cyan-500 via-sky-500 to-indigo-500" />
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-black/35 bg-[#ffdb67] px-3 py-1.5 text-sm font-semibold uppercase tracking-[0.12em]">
                        <AppIcon name="user" class="h-4 w-4" />
                        Profile Information
                    </div>
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-3xl"
                    />
                </div>

                <div
                    class="relative overflow-hidden rounded-3xl border border-black/55 bg-[#f1d368] p-6 sm:p-8"
                >
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-amber-500 via-orange-500 to-red-500" />
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-black/35 bg-[#ffdb67] px-3 py-1.5 text-sm font-semibold uppercase tracking-[0.12em]">
                        <AppIcon name="lock" class="h-4 w-4" />
                        Password Security
                    </div>
                    <UpdatePasswordForm class="max-w-3xl" />
                </div>

                <div
                    class="relative overflow-hidden rounded-3xl border border-black/55 bg-[#f1d368] p-6 sm:p-8"
                >
                    <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-rose-500 via-red-500 to-orange-500" />
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-black/35 bg-[#ffdb67] px-3 py-1.5 text-sm font-semibold uppercase tracking-[0.12em]">
                        <AppIcon name="shield" class="h-4 w-4" />
                        Account Controls
                    </div>
                    <DeleteUserForm class="max-w-3xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
