<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    phone: user.phone ?? '',
    company: user.company ?? '',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-2xl font-extrabold text-black">
                Profile Information
            </h2>

            <p class="mt-2 text-sm text-black/75">
                Update your account's profile information and email address.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6"
        >
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-2xl border-black/35 bg-[#ffdb67] px-4 py-3 text-base shadow-none focus:border-black focus:ring-0"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-2xl border-black/35 bg-[#ffdb67] px-4 py-3 text-base shadow-none focus:border-black focus:ring-0"
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
                        class="mt-1 block w-full rounded-2xl border-black/35 bg-[#ffdb67] px-4 py-3 text-base shadow-none focus:border-black focus:ring-0"
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
                        class="mt-1 block w-full rounded-2xl border-black/35 bg-[#ffdb67] px-4 py-3 text-base shadow-none focus:border-black focus:ring-0"
                        v-model="form.company"
                        autocomplete="organization"
                    />

                    <InputError class="mt-2" :message="form.errors.company" />
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-black/80">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm font-semibold text-black/75 underline hover:text-black focus:outline-none focus:ring-0"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-emerald-800"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton
                    :disabled="form.processing"
                    class="!rounded-full !bg-black !px-7 !py-3 !text-sm !font-semibold !tracking-[0.14em] !text-[#ffdb67] hover:!bg-black/90"
                >
                    Save
                </PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm font-semibold text-emerald-800"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
