<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    project_title: '',
    project_description: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Hire Me Registration" />

        <div class="mb-5 rounded-xl border border-emerald-700/30 bg-emerald-100/70 p-4 text-sm text-emerald-900">
            Complete this form to start your project request. After registration you will be redirected to your dashboard.
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="name" value="Full Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email Address" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <InputLabel for="phone" value="Phone Number" />
                    <TextInput
                        id="phone"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.phone"
                        autocomplete="tel"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <div>
                    <InputLabel for="company" value="Company" />
                    <TextInput
                        id="company"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.company"
                        autocomplete="organization"
                    />
                    <InputError class="mt-2" :message="form.errors.company" />
                </div>
            </div>

            <div>
                <InputLabel for="project_title" value="Project Title" />
                <TextInput
                    id="project_title"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.project_title"
                    required
                    maxlength="255"
                />
                <InputError class="mt-2" :message="form.errors.project_title" />
            </div>

            <div>
                <InputLabel for="project_description" value="Project Description" />
                <textarea
                    id="project_description"
                    v-model="form.project_description"
                    rows="5"
                    class="mt-1 block w-full rounded-2xl border border-black/35 bg-[#ffdb67] px-4 py-3 text-base shadow-none focus:border-black focus:ring-0"
                    placeholder="Tell us your project goals, scope, and timeline."
                />
                <InputError class="mt-2" :message="form.errors.project_description" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <InputLabel for="password" value="Password" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Confirm Password" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="text-sm font-semibold underline underline-offset-2 hover:text-black/70"
                >
                    Already registered?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Create Account & Track Project
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
