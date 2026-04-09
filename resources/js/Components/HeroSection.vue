<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const words = computed(() => {
    const rawWords = Array.isArray(props.content?.typing_words)
        ? props.content.typing_words
        : [];
    const sanitizedWords = rawWords
        .map((word) => String(word).trim())
        .filter(Boolean);

    return sanitizedWords.length > 0 ? sanitizedWords : ['Developer'];
});

const typedText = ref('');
const typing = ref(false);
const activeWordIndex = ref(0);

const typeDelay = 120;
const deleteDelay = 32;
const holdDelay = 1500;

let timerId = null;
let charIndex = 0;

const clearTimer = () => {
    if (timerId) {
        window.clearTimeout(timerId);
        timerId = null;
    }
};

const typeNext = () => {
    const currentWord = words.value[activeWordIndex.value] ?? '';

    if (charIndex < currentWord.length) {
        typing.value = true;
        typedText.value += currentWord.charAt(charIndex);
        charIndex += 1;
        timerId = window.setTimeout(typeNext, typeDelay);
        return;
    }

    typing.value = false;
    timerId = window.setTimeout(deleteNext, holdDelay);
};

const deleteNext = () => {
    const currentWord = words.value[activeWordIndex.value] ?? '';

    if (charIndex > 0) {
        typing.value = true;
        typedText.value = currentWord.substring(0, charIndex - 1);
        charIndex -= 1;
        timerId = window.setTimeout(deleteNext, deleteDelay);
        return;
    }

    typing.value = false;
    activeWordIndex.value = (activeWordIndex.value + 1) % words.value.length;
    timerId = window.setTimeout(typeNext, typeDelay + 900);
};

const restartTyping = () => {
    clearTimer();
    typedText.value = '';
    typing.value = false;
    activeWordIndex.value = 0;
    charIndex = 0;
    timerId = window.setTimeout(typeNext, holdDelay + 120);
};

const viewWorkHref = computed(() => {
    const target = props.content?.view_work_target?.trim() || 'projects';

    return target.startsWith('#') ? target : `#${target}`;
});

onMounted(() => {
    restartTyping();
});

onBeforeUnmount(() => {
    clearTimer();
});

watch(words, () => {
    restartTyping();
});
</script>

<template>
    <div class="grid items-center gap-8 lg:gap-10 md:grid-cols-[1.2fr_0.8fr]">
        <div>
            <h4 class="mb-3 text-xl font-medium uppercase tracking-[0.08em] md:text-2xl">
                {{ content.tagline || 'Hi, I am a freelancer' }}
            </h4>
            <h1 class="mb-3 flex min-h-[64px] items-center text-[clamp(2rem,4.6vw,4.05rem)] font-bold leading-none uppercase tracking-tight">
                <span>{{ typedText }}</span>
                <span class="ml-2 inline-block text-black/80" :class="typing ? 'animate-none' : 'animate-pulse'">|</span>
            </h1>
            <p class="max-w-xl text-lg md:text-xl">
                {{ content.location || 'based in Los Angeles, California.' }}
            </p>
            <div class="mt-7 flex flex-wrap items-center gap-3">
                <a
                    :href="viewWorkHref"
                    class="rounded-full bg-black px-7 py-2.5 text-base font-semibold text-[#ffdb67] transition hover:bg-black/90"
                >
                    {{ content.view_work_label || 'View My Work' }}
                </a>
                <Link
                    :href="content.hire_me_href || '/register'"
                    class="rounded-full border border-black px-7 py-2.5 text-base font-semibold transition hover:bg-black hover:text-[#ffdb67]"
                >
                    {{ content.hire_me_label || 'Hire Me' }}
                </Link>
            </div>
        </div>

        <div class="mx-auto w-full max-w-[330px] lg:max-w-[360px]">
            <div class="hero-image-shell relative isolate aspect-square max-h-[380px] max-w-[380px]">
                <div class="hero-waves-top" aria-hidden="true">
                    <span />
                    <span />
                    <span />
                    <span />
                </div>
                <img
                    :src="content.image || '/legacy/images/hero.png'"
                    :alt="content.image_alt || 'Hero'"
                    class="hero-image relative z-10 h-full w-full rounded-full border-2 border-black object-cover"
                >
                <div class="hero-waves-bottom" aria-hidden="true">
                    <span />
                    <span />
                    <span />
                    <span />
                </div>
            </div>
        </div>
    </div>
</template>
