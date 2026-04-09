<script setup>
import Footer from '@/Components/Footer.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const showingNavigationDropdown = ref(false);
const userMenuOpen = ref(false);
const page = usePage();

const isAdmin = computed(() => page.props.auth.user?.role === 'admin');

const navigationLinks = computed(() => {
    const links = [
        {
            key: 'dashboard',
            label: 'Dashboard',
            href: route('dashboard'),
            active: route().current('dashboard'),
        },
    ];

    if (isAdmin.value) {
        links.push(
            {
                key: 'home-content',
                label: 'Home Content',
                href: route('admin.home-content.edit'),
                active: route().current('admin.home-content.edit'),
            },
            {
                key: 'project-queue',
                label: 'Project Queue',
                href: route('admin.projects.index'),
                active: route().current('admin.projects.index'),
            }
        );
    }

    return links;
});

const toggleMobileMenu = () => {
    showingNavigationDropdown.value = !showingNavigationDropdown.value;
};

const closeMobileMenu = () => {
    showingNavigationDropdown.value = false;
};
</script>

<template>
    <div class="skeuo-page flex min-h-screen flex-col text-black">
        <nav class="skeuo-nav sticky top-0 z-50 border-b border-black/20">
            <div class="mx-auto max-w-[1900px] px-6 sm:px-10 lg:px-16">
                <div class="flex min-h-[80px] items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            class="skeuo-button-soft relative h-11 w-11 rounded-full !px-0 !py-0 xl:hidden"
                            :aria-expanded="showingNavigationDropdown ? 'true' : 'false'"
                            aria-label="Toggle navigation"
                            @click="toggleMobileMenu"
                        >
                            <span
                                class="absolute left-1/2 top-[14px] h-0.5 w-5 -translate-x-1/2 bg-black transition"
                                :class="showingNavigationDropdown ? 'translate-y-[7px] rotate-45' : ''"
                            />
                            <span
                                class="absolute left-1/2 top-1/2 h-0.5 w-5 -translate-x-1/2 -translate-y-1/2 bg-black transition"
                                :class="showingNavigationDropdown ? 'opacity-0' : ''"
                            />
                            <span
                                class="absolute left-1/2 bottom-[14px] h-0.5 w-5 -translate-x-1/2 bg-black transition"
                                :class="showingNavigationDropdown ? '-translate-y-[7px] -rotate-45' : ''"
                            />
                        </button>

                        <Link :href="route('dashboard')" class="flex items-center gap-3">
                            <img
                                src="/legacy/images/logo.png"
                                alt="Portfolio"
                                class="h-12 w-auto object-contain sm:h-14"
                            >
                        </Link>
                    </div>

                    <div class="hidden items-center gap-3 md:flex">
                        <div class="relative">
                            <button
                                type="button"
                                class="skeuo-button-soft inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold"
                                @click="userMenuOpen = !userMenuOpen"
                            >
                                {{ $page.props.auth.user.name }}
                                <svg
                                    class="h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.22 7.22a.75.75 0 011.06 0L10 10.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 8.28a.75.75 0 010-1.06z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>

                            <div
                                v-if="userMenuOpen"
                                class="skeuo-card absolute right-0 mt-2 w-52 p-2 shadow-xl"
                            >
                                <Link
                                    :href="route('profile.edit')"
                                    class="block rounded-xl px-3 py-2 text-sm font-medium hover:bg-black/10"
                                    @click="userMenuOpen = false"
                                >
                                    Profile
                                </Link>
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium hover:bg-black/10"
                                    @click="userMenuOpen = false"
                                >
                                    Log Out
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="relative flex flex-1">
            <div
                class="fixed inset-0 z-40 bg-black/35 transition xl:hidden"
                :class="showingNavigationDropdown ? 'opacity-100' : 'pointer-events-none opacity-0'"
                @click="closeMobileMenu"
            />

            <aside
                class="fixed left-0 top-0 z-50 h-full w-72 border-r border-black/20 skeuo-nav px-4 pb-6 pt-24 transition-transform duration-300 xl:hidden"
                :class="showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full'"
            >
                <nav class="space-y-3">
                    <Link
                        v-for="link in navigationLinks"
                        :key="`mobile-${link.key}`"
                        :href="link.href"
                        class="block w-full px-4 py-2.5 text-left text-sm font-semibold uppercase tracking-[0.13em]"
                        :class="link.active ? 'skeuo-button' : 'skeuo-button-soft'"
                        @click="closeMobileMenu"
                    >
                        {{ link.label }}
                    </Link>
                </nav>

                <div class="mt-6 border-t border-black/20 pt-5">
                    <p class="text-sm font-semibold">{{ $page.props.auth.user.name }}</p>
                    <p class="text-xs text-black/70">{{ $page.props.auth.user.email }}</p>
                    <div class="mt-3 space-y-2">
                        <Link
                            :href="route('profile.edit')"
                            class="skeuo-button-soft block w-full px-4 py-2 text-left text-sm font-medium"
                            @click="closeMobileMenu"
                        >
                            Profile
                        </Link>
                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="skeuo-button-soft block w-full px-4 py-2 text-left text-sm font-medium"
                            @click="closeMobileMenu"
                        >
                            Log Out
                        </Link>
                    </div>
                </div>
            </aside>

            <aside class="hidden w-72 shrink-0 border-r border-black/20 xl:block">
                <div class="sticky top-[88px] px-6 py-7">
                    <nav class="space-y-3">
                        <Link
                            v-for="link in navigationLinks"
                            :key="`desktop-${link.key}`"
                            :href="link.href"
                            class="block w-full px-4 py-2.5 text-left text-sm font-semibold uppercase tracking-[0.13em]"
                            :class="link.active ? 'skeuo-button' : 'skeuo-button-soft'"
                        >
                            {{ link.label }}
                        </Link>
                    </nav>

                    <div class="mt-8 border-t border-black/20 pt-4 text-sm text-black/75">
                        <p class="font-semibold">{{ $page.props.auth.user.name }}</p>
                        <p class="mt-0.5 text-xs">{{ $page.props.auth.user.email }}</p>
                    </div>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header
                    v-if="$slots.header"
                    class="border-b border-black/20"
                >
                    <div class="mx-auto max-w-[1320px] px-4 py-5 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <main class="flex-1">
                    <slot />
                </main>
            </div>
        </div>

        <Footer />
    </div>
</template>
