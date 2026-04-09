<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-2xl font-extrabold text-black">
                Delete Account
            </h2>

            <p class="mt-2 text-sm text-black/75">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <DangerButton
            class="!rounded-full !border !border-black/20 !bg-red-600 !px-7 !py-3 !text-sm !font-semibold !tracking-[0.14em]"
            @click="confirmUserDeletion"
        >
            Delete Account
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="border border-black/40 bg-[#f1d368] p-6">
                <h2
                    class="text-lg font-bold text-black"
                >
                    Are you sure you want to delete your account?
                </h2>

                <p class="mt-2 text-sm text-black/75">
                    Once your account is deleted, all of its resources and data
                    will be permanently deleted. Please enter your password to
                    confirm you would like to permanently delete your account.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="password"
                        value="Password"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full rounded-2xl border-black/35 bg-[#ffdb67] px-4 py-3 text-base shadow-none focus:border-black focus:ring-0 sm:w-3/4"
                        placeholder="Password"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton
                        class="!rounded-full !border-black/35 !bg-[#ffdb67] !px-6 !py-2.5 !text-xs !text-black hover:!bg-black hover:!text-[#ffdb67]"
                        @click="closeModal"
                    >
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3 !rounded-full !border !border-black/20 !bg-red-600 !px-6 !py-2.5 !text-xs"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
