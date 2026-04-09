<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    scrolled: {
        type: Boolean,
        default: false,
    },
    mobileMenuOpen: {
        type: Boolean,
        default: false,
    },
    viewWorkLabel: {
        type: String,
        default: 'View Work',
    },
    viewWorkTarget: {
        type: String,
        default: 'projects',
    },
    hireMeLabel: {
        type: String,
        default: 'Hire Me',
    },
    hireMeHref: {
        type: String,
        default: '/register',
    },
    showMobileToggle: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['toggle', 'logo-click']);
const page = usePage();

const viewWorkHref = computed(() => {
    const target = props.viewWorkTarget?.trim() || 'projects';

    return target.startsWith('#') ? target : `#${target}`;
});

const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
const isHomeRoute = computed(() => route().current('home'));
</script>

<template>
    <header
        class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
        :class="scrolled ? 'skeuo-nav' : 'bg-transparent'"
    >
        <div class="mx-auto flex w-full max-w-[1900px] items-center justify-between px-6 py-4 sm:px-10 lg:px-16">
            <button
                v-if="isHomeRoute"
                type="button"
                class="flex items-center gap-3"
                @click="emit('logo-click')"
            >
                <img
                    src="/legacy/images/logo.png"
                    alt="Portify"
                    class="h-16 w-auto object-contain sm:h-20"
                >
            </button>
            <Link v-else :href="`${route('home')}#top`" class="flex items-center gap-3">
                <img
                    src="/legacy/images/logo.png"
                    alt="Portify"
                    class="h-16 w-auto object-contain sm:h-20"
                >
            </Link>

            <div class="hidden items-center gap-4 lg:flex">
                <a
                    v-if="!route().current('home')"
                    :href="viewWorkHref"
                    class="skeuo-button-soft px-5 py-2 text-sm font-medium uppercase tracking-[0.14em]"
                >
                    {{ viewWorkLabel }}
                </a>
                <Link
                    v-if="!isAuthenticated"
                    :href="route('login')"
                    class="skeuo-button-soft px-5 py-2 text-sm font-semibold uppercase tracking-[0.14em]"
                >
                    Login
                </Link>
                <Link
                    v-else
                    :href="route('dashboard')"
                    class="skeuo-button-soft px-5 py-2 text-sm font-semibold uppercase tracking-[0.14em]"
                >
                    Dashboard
                </Link>
                <Link
                    v-if="!route().current('home')"
                    :href="hireMeHref"
                    class="skeuo-button px-5 py-2 text-sm font-semibold uppercase tracking-[0.14em]"
                >
                    {{ hireMeLabel }}
                </Link>
            </div>

            <div class="flex items-center gap-3 lg:hidden">
                <Link
                    v-if="!isAuthenticated"
                    :href="route('login')"
                    class="skeuo-button-soft px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em]"
                >
                    Login
                </Link>
                <Link
                    v-else
                    :href="route('dashboard')"
                    class="skeuo-button-soft px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em]"
                >
                    Dashboard
                </Link>

                <button
                    v-if="showMobileToggle"
                    type="button"
                    class="skeuo-button-soft relative h-11 w-11 rounded-full !px-0 !py-0"
                    :aria-expanded="mobileMenuOpen ? 'true' : 'false'"
                    aria-label="Toggle navigation"
                    @click="$emit('toggle')"
                >
                    <span
                        class="absolute left-1/2 top-[14px] h-0.5 w-5 -translate-x-1/2 bg-black transition"
                        :class="mobileMenuOpen ? 'translate-y-[7px] rotate-45' : ''"
                    />
                    <span
                        class="absolute left-1/2 top-1/2 h-0.5 w-5 -translate-x-1/2 -translate-y-1/2 bg-black transition"
                        :class="mobileMenuOpen ? 'opacity-0' : ''"
                    />
                    <span
                        class="absolute left-1/2 bottom-[14px] h-0.5 w-5 -translate-x-1/2 bg-black transition"
                        :class="mobileMenuOpen ? '-translate-y-[7px] -rotate-45' : ''"
                    />
                </button>
            </div>
        </div>
    </header>
</template>
