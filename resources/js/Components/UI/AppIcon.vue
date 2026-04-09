<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
});

const iconPath = computed(() => {
    const icons = {
        list: ['M4 7h16', 'M4 12h16', 'M4 17h10'],
        clock: ['M12 8v4l3 2', 'M4 12a8 8 0 1 0 16 0a8 8 0 1 0 -16 0'],
        progress: ['M7 17L17 7', 'M9 7h8v8'],
        check: ['m6 12 4 4 8-8'],
        briefcase: ['M9 6V4h6v2', 'M4 8h16v10H4z', 'M4 12h16'],
        server: ['M4 7h16v4H4z', 'M4 13h16v4H4z', 'M7 9h.01', 'M7 15h.01'],
        database: ['M12 5c-4.4 0-8 1.3-8 3s3.6 3 8 3s8-1.3 8-3s-3.6-3-8-3', 'M4 8v4c0 1.7 3.6 3 8 3s8-1.3 8-3V8', 'M4 12v4c0 1.7 3.6 3 8 3s8-1.3 8-3v-4'],
        code: ['m8 8-4 4 4 4', 'm16 8 4 4-4 4', 'm13 5-2 14'],
        chart: ['M5 18V10', 'M12 18V6', 'M19 18v-4', 'M4 18h16'],
        certificate: ['M8 4h8l4 4v12H4V4z', 'M8 13h8', 'M8 9h5'],
        chat: ['M4 5h16v10H8l-4 4V5z'],
        user: ['M12 12a4 4 0 1 0 0-8a4 4 0 1 0 0 8', 'M5 20a7 7 0 0 1 14 0'],
        email: ['M4 6h16v12H4z', 'm4 7 8 6 8-6'],
        phone: ['M6 3h4l1 4-2 2a15 15 0 0 0 6 6l2-2 4 1v4c0 1-1 2-2 2C10 20 4 14 4 5c0-1 1-2 2-2'],
        location: ['M12 21s6-5.3 6-10a6 6 0 1 0-12 0c0 4.7 6 10 6 10', 'M12 13a2 2 0 1 0 0-4a2 2 0 1 0 0 4'],
        shield: ['M12 3l7 3v6c0 5-3.5 8-7 9-3.5-1-7-4-7-9V6l7-3', 'm9 12 2 2 4-4'],
        lock: ['M7 11V8a5 5 0 0 1 10 0v3', 'M6 11h12v9H6z'],
        activity: ['M4 13h4l2-4 3 7 2-3h5'],
        note: ['M6 4h12v16H6z', 'M9 8h6', 'M9 12h6'],
        calendar: ['M8 3v3', 'M16 3v3', 'M4 8h16', 'M5 6h14v14H5z'],
        sparkles: ['m12 3 1.6 3.4L17 8l-3.4 1.6L12 13l-1.6-3.4L7 8l3.4-1.6L12 3', 'm5 14 .8 1.8L8 17l-2.2 1.2L5 20l-.8-1.8L2 17l2.2-1.2L5 14', 'm19 14 .8 1.8L22 17l-2.2 1.2L19 20l-.8-1.8L16 17l2.2-1.2L19 14'],
        quote: ['M9 10H5V6h4v4zm10 0h-4V6h4v4z', 'M5 10v4a4 4 0 0 0 4 4', 'M15 10v4a4 4 0 0 0 4 4'],
    };

    return icons[props.name] ?? icons.sparkles;
});
</script>

<template>
    <svg
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
    >
        <path
            v-for="(segment, index) in iconPath"
            :key="`${name}-${index}`"
            :d="segment"
        />
    </svg>
</template>
