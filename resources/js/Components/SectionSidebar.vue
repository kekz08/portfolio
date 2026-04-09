<script setup>
import { computed } from 'vue';

const props = defineProps({
    sections: {
        type: Array,
        required: true,
    },
    activeSection: {
        type: String,
        required: true,
    },
    open: {
        type: Boolean,
        default: false,
    },
    contactEmail: {
        type: String,
        default: '',
    },
    contactPhone: {
        type: String,
        default: '',
    },
});

defineEmits(['select', 'close']);

const emailHref = computed(() => {
    if (!props.contactEmail) {
        return '';
    }

    return `mailto:${props.contactEmail}`;
});

const phoneHref = computed(() => {
    if (!props.contactPhone) {
        return '';
    }

    const normalizedPhone = props.contactPhone.replace(/[^+\d]/g, '');

    return `tel:${normalizedPhone}`;
});
</script>

<template>
    <aside class="hidden lg:fixed lg:right-8 lg:top-32 lg:block lg:w-[280px] lg:pr-3 xl:right-14 xl:w-[300px] xl:pr-4">
        <div class="skeuo-card relative overflow-hidden">
            <ul class="divide-y divide-black/10">
                <li
                    v-for="section in sections"
                    :key="section.id"
                >
                    <button
                        type="button"
                        class="group relative block w-full px-7 py-3 text-left text-[1.05rem] font-medium uppercase transition"
                        :class="activeSection === section.id ? 'border-l-2 border-black bg-black/15 shadow-[inset_0_1px_0_rgba(255,255,255,0.25)]' : 'border-l-2 border-transparent hover:border-black hover:bg-black/10'"
                        @click="$emit('select', section.id)"
                    >
                        <span class="mr-2 text-sm">{{ section.number }}</span>
                        <span>{{ section.label }}</span>
                    </button>
                </li>
            </ul>

            <div class="space-y-2 border-t border-black/20 px-6 pb-5 pt-5 text-[1.35rem] leading-snug">
                <a
                    v-if="contactEmail"
                    :href="emailHref"
                    class="block break-all hover:underline"
                >
                    {{ contactEmail }}
                </a>
                <a
                    v-if="contactPhone"
                    :href="phoneHref"
                    class="block break-words hover:underline"
                >
                    {{ contactPhone }}
                </a>
            </div>
        </div>
    </aside>

    <div
        class="fixed inset-0 z-50 bg-black/45 transition lg:hidden"
        :class="open ? 'opacity-100' : 'pointer-events-none opacity-0'"
        @click="$emit('close')"
    />
    <aside
        class="fixed right-0 top-0 z-[60] h-full w-[290px] bg-[#ffdb67] p-6 shadow-2xl transition lg:hidden"
        :class="open ? 'translate-x-0' : 'translate-x-full'"
    >
        <div class="mb-5 flex items-center justify-between">
            <h2 class="text-lg font-semibold uppercase tracking-[0.14em]">Menu</h2>
            <button
                type="button"
                class="skeuo-button-soft px-3 py-1 text-sm font-medium"
                @click="$emit('close')"
            >
                Close
            </button>
        </div>
        <ul class="space-y-2">
            <li
                v-for="section in sections"
                :key="section.id"
            >
                <button
                    type="button"
                    class="group relative block w-full px-4 py-2 text-left text-[0.97rem] font-medium uppercase transition"
                    :class="activeSection === section.id ? 'border-l-2 border-black bg-black/15' : 'border-l-2 border-transparent hover:border-black hover:bg-black/10'"
                    @click="$emit('select', section.id)"
                >
                    <span class="mr-2 text-xs">{{ section.number }}</span>
                    <span>{{ section.label }}</span>
                </button>
            </li>
        </ul>
        <div class="space-y-2 px-4 pt-8 text-sm">
            <a
                v-if="contactEmail"
                :href="emailHref"
                class="block break-all hover:underline"
            >
                {{ contactEmail }}
            </a>
            <a
                v-if="contactPhone"
                :href="phoneHref"
                class="block break-words hover:underline"
            >
                {{ contactPhone }}
            </a>
        </div>
    </aside>
</template>
