<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import Footer from '@/Components/Footer.vue';
import HeroSection from '@/Components/HeroSection.vue';
import Navbar from '@/Components/Navbar.vue';
import PortfolioCard from '@/Components/PortfolioCard.vue';
import SectionSidebar from '@/Components/SectionSidebar.vue';
import AppIcon from '@/Components/UI/AppIcon.vue';

const props = defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
});

const fallbackSections = [
    { id: 'top', number: '01', label: 'Top' },
    { id: 'about_me', number: '02', label: 'About Me' },
    { id: 'services', number: '03', label: 'My Services' },
    { id: 'attainments', number: '04', label: 'Skills' },
    { id: 'fun_facts', number: '05', label: 'Facts' },
    { id: 'experience', number: '06', label: 'Experiences' },
    { id: 'projects', number: '07', label: 'Projects' },
    { id: 'certificates', number: '08', label: 'Certificates' },
    { id: 'how_i_work', number: '09', label: 'How I Work' },
    { id: 'blogs', number: '10', label: 'Blogs' },
    { id: 'testimonials', number: '11', label: 'Testimonials' },
    { id: 'contact', number: '12', label: 'Contact' },
];

const sections = computed(() => {
    const incoming = Array.isArray(props.content?.sections) ? props.content.sections : [];

    return fallbackSections.map((section, index) => ({
        id: section.id,
        number: String(incoming[index]?.number ?? section.number),
        label: String(incoming[index]?.label ?? section.label),
    }));
});

const navbar = computed(() => props.content?.navbar ?? {});
const sidebar = computed(() => props.content?.sidebar ?? {});
const hero = computed(() => props.content?.hero ?? {});
const about = computed(() => props.content?.about ?? {});
const servicesContent = computed(() => props.content?.services ?? {});
const skillsContent = computed(() => props.content?.skills ?? {});
const factsContent = computed(() => props.content?.facts ?? {});
const experienceContent = computed(() => props.content?.experience ?? {});
const projectsContent = computed(() => props.content?.projects ?? {});
const certificatesContent = computed(() => props.content?.certificates ?? {});
const processContent = computed(() => props.content?.process ?? {});
const blogsContent = computed(() => props.content?.blogs ?? {});
const testimonialsContent = computed(() => props.content?.testimonials ?? {});
const contactContent = computed(() => props.content?.contact ?? {});
const footerContent = computed(() => props.content?.footer ?? {});
const nextChapterLabel = computed(() => props.content?.next_chapter_label || 'Next Chapter');

const serviceCards = computed(() => (Array.isArray(servicesContent.value?.cards) ? servicesContent.value.cards : []));
const skills = computed(() => (Array.isArray(skillsContent.value?.items) ? skillsContent.value.items : []));
const facts = computed(() => (Array.isArray(factsContent.value?.items) ? factsContent.value.items : []));
const timeline = computed(() => (Array.isArray(experienceContent.value?.timeline) ? experienceContent.value.timeline : []));
const projectItems = computed(() => (Array.isArray(projectsContent.value?.items) ? projectsContent.value.items : []));
const certificateItems = computed(() => (Array.isArray(certificatesContent.value?.items) ? certificatesContent.value.items : []));
const processSteps = computed(() => (Array.isArray(processContent.value?.steps) ? processContent.value.steps : []));
const blogCards = computed(() => (Array.isArray(blogsContent.value?.cards) ? blogsContent.value.cards : []));
const testimonials = computed(() => (Array.isArray(testimonialsContent.value?.items) ? testimonialsContent.value.items : []));

const activeSection = ref('top');
const mobileMenuOpen = ref(false);
const scrolled = ref(false);
const contactSubmitted = ref(false);

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};

const hasSection = (id) => sections.value.some((section) => section.id === id);

const setActiveSection = (id, { updateHash = true, behavior = 'smooth' } = {}) => {
    if (!hasSection(id)) {
        return;
    }

    activeSection.value = id;

    if (updateHash) {
        window.history.replaceState(null, '', `${window.location.pathname}#${id}`);
    }

    window.scrollTo({ top: 0, behavior });
    closeMobileMenu();
};

const openSection = (id) => {
    setActiveSection(id, { updateHash: true, behavior: 'smooth' });
};

const onScroll = () => {
    scrolled.value = window.scrollY > 24;
};

const syncFromHash = () => {
    const id = window.location.hash.replace('#', '').trim();

    if (id && hasSection(id)) {
        setActiveSection(id, { updateHash: false, behavior: 'auto' });
        return;
    }

    activeSection.value = sections.value[0]?.id ?? 'top';
};

const onContactSubmit = () => {
    contactSubmitted.value = true;
};

const sectionIndex = (id) => sections.value.findIndex((section) => section.id === id);

const sectionPosition = (id) => {
    const index = sectionIndex(id);

    if (index === -1) {
        return '';
    }

    const total = sections.value.length || 1;

    return `${String(index + 1).padStart(2, '0')}/${String(total).padStart(2, '0')}`;
};

const nextSectionId = (id) => {
    const index = sectionIndex(id);

    if (index === -1 || index >= sections.value.length - 1) {
        return null;
    }

    return sections.value[index + 1].id;
};

const openNextSection = (id) => {
    const next = nextSectionId(id);

    if (next) {
        openSection(next);
    }
};

const normalizeSkillValue = (value) => {
    const parsed = Number(value);

    if (!Number.isFinite(parsed)) {
        return 0;
    }

    return Math.min(100, Math.max(0, parsed));
};

const aboutEmailHref = computed(() => {
    const email = typeof about.value?.email === 'string'
        ? about.value.email.trim()
        : '';

    if (!email) {
        return '';
    }

    return `mailto:${email}`;
});

const toAssetHref = (path) => {
    if (typeof path !== 'string' || !path.trim()) {
        return '';
    }

    return encodeURI(path.trim()).replaceAll('#', '%23');
};

const serviceIcon = (index) => ['code', 'server', 'database'][index % 3];
const factIcon = (index) => ['check', 'chart', 'briefcase', 'activity'][index % 4];
const timelineIcon = (index) => ['briefcase', 'code', 'server', 'chart'][index % 4];
const stepIcon = (index) => ['chat', 'note', 'sparkles', 'check'][index % 4];

onMounted(() => {
    onScroll();

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('hashchange', syncFromHash);
    syncFromHash();
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('hashchange', syncFromHash);
});
</script>

<template>
    <Head :title="content.meta_title || 'Portfolio'" />

    <div class="skeuo-page flex min-h-screen flex-col bg-[#ffdb67] text-black">
        <Navbar
            :scrolled="scrolled"
            :mobile-menu-open="mobileMenuOpen"
            :view-work-label="navbar.view_work_label || 'View Work'"
            :view-work-target="navbar.view_work_target || 'projects'"
            :hire-me-label="navbar.hire_me_label || 'Hire Me'"
            :hire-me-href="navbar.hire_me_href || '/register'"
            @toggle="mobileMenuOpen = !mobileMenuOpen"
            @logo-click="openSection('top')"
        />

        <SectionSidebar
            :sections="sections"
            :active-section="activeSection"
            :open="mobileMenuOpen"
            :contact-email="sidebar.contact_email || ''"
            :contact-phone="sidebar.contact_phone || ''"
            @select="openSection"
            @close="closeMobileMenu"
        />

        <main class="mx-auto w-full max-w-[1700px] flex-1 px-6 pb-16 pt-28 sm:px-10 lg:px-12 lg:pr-[320px] xl:px-14 xl:pr-[360px] 2xl:px-16 2xl:pr-[390px]">
            <section
                id="top"
                v-show="activeSection === 'top'"
                :aria-hidden="activeSection !== 'top'"
                :inert="activeSection !== 'top'"
                class="scroll-mt-32 flex min-h-[calc(100vh-7rem)] flex-col justify-between pb-20 pt-12"
            >
                <HeroSection :content="hero" />
                <button
                    v-if="nextSectionId('top')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('top')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('top') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="about_me"
                v-show="activeSection === 'about_me'"
                :aria-hidden="activeSection !== 'about_me'"
                :inert="activeSection !== 'about_me'"
                class="scroll-mt-28 pb-24"
            >
                <div class="relative mb-10 overflow-hidden">
                    <h2 class="pointer-events-none absolute left-0 top-0 text-[clamp(3rem,9vw,7rem)] font-extrabold uppercase tracking-tight text-black/5">
                        {{ about.background_label || 'About Me' }}
                    </h2>
                    <div class="relative border-l-2 border-black pl-7 pt-10">
                        <p class="text-[clamp(0.95rem,1.1vw,1.1rem)] font-medium">{{ about.intro_label || 'Some Word About Me' }}</p>
                        <h3 class="mt-3 text-[clamp(1.9rem,3.8vw,3.5rem)] font-extrabold leading-none">{{ about.title || 'Know Me More' }}</h3>
                    </div>
                </div>

                <div class="grid items-center gap-10 xl:grid-cols-[1.2fr_0.8fr]">
                    <div>
                        <h4 class="mb-6 text-[clamp(1.55rem,2.2vw,2.1rem)] font-semibold leading-tight">{{ about.headline || "I'm Emily Davis, a Web Developer" }}</h4>
                        <p class="max-w-5xl text-[clamp(1rem,0.95vw,1.08rem)] leading-[1.6]">
                            {{ about.description || '' }}
                        </p>

                        <div class="mt-9 grid gap-6 text-[clamp(0.9rem,0.9vw,1rem)] sm:grid-cols-3">
                            <div>
                                <p class="mb-1 font-medium">Email:</p>
                                <a v-if="about.email" :href="aboutEmailHref" class="hover:underline">{{ about.email }}</a>
                                <p v-else>-</p>
                            </div>
                            <div>
                                <p class="mb-1 font-medium">Date of Birth:</p>
                                <p>{{ about.date_of_birth || '-' }}</p>
                            </div>
                            <div>
                                <p class="mb-1 font-medium">Location:</p>
                                <p>{{ about.location || '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mx-auto w-full max-w-[480px]">
                        <div class="relative">
                            <div class="absolute inset-0 translate-x-3 -translate-y-2 border border-black/60" />
                            <div class="absolute inset-0 -translate-x-2 translate-y-3 border border-black/60" />
                            <div class="relative border border-black/70 bg-[#f1d368] p-8 text-center">
                                <p class="text-[clamp(4.4rem,8vw,7.6rem)] font-black leading-none">{{ about.experience_years || '10' }}</p>
                                <p class="text-[clamp(0.95rem,1.05vw,1.15rem)] font-medium">{{ about.experience_label || 'Years of experience' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    v-if="nextSectionId('about_me')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('about_me')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('about_me') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="services"
                v-show="activeSection === 'services'"
                :aria-hidden="activeSection !== 'services'"
                :inert="activeSection !== 'services'"
                class="scroll-mt-28 pb-24"
            >
                <div class="mb-10">
                    <p class="text-[1.2rem] uppercase tracking-[0.14em] text-black/70">{{ servicesContent.label || '03 My Services' }}</p>
                    <h3 class="mt-3 text-[clamp(2.05rem,4.1vw,3.85rem)] font-extrabold leading-tight">{{ servicesContent.title || 'How I Can Help Your Brand' }}</h3>
                </div>

                <div class="grid gap-6 lg:grid-cols-3">
                    <article
                        v-for="(service, index) in serviceCards"
                        :key="`${service.title}-${index}`"
                        class="relative overflow-hidden rounded-3xl border border-black/70 bg-[#f1d368] p-6 transition duration-300 hover:-translate-y-1"
                    >
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500" />
                        <div class="absolute -inset-1 -z-10 border border-black/40 opacity-0 transition duration-300 hover:opacity-100" />
                        <div class="mb-6 inline-flex h-14 w-14 items-center justify-center rounded-full border border-black bg-[#ffdb67] text-2xl font-semibold">
                            <AppIcon :name="serviceIcon(index)" class="h-7 w-7" />
                        </div>
                        <h4 class="mb-4 text-[2rem] font-semibold leading-tight">{{ service.title }}</h4>
                        <ul class="space-y-2 text-base md:text-lg">
                            <li
                                v-for="(item, itemIndex) in service.items || []"
                                :key="`${item}-${itemIndex}`"
                                class="flex items-center gap-2"
                            >
                                <span class="text-lg">&bull;</span>
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                    </article>
                </div>

                <button
                    v-if="nextSectionId('services')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('services')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('services') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="attainments"
                v-show="activeSection === 'attainments'"
                :aria-hidden="activeSection !== 'attainments'"
                :inert="activeSection !== 'attainments'"
                class="scroll-mt-28 pb-24"
            >
                <div class="mb-10">
                    <p class="text-[1.2rem] uppercase tracking-[0.14em] text-black/70">{{ skillsContent.label || '04 Skills' }}</p>
                    <h3 class="mt-3 text-[clamp(2.05rem,4.1vw,3.85rem)] font-extrabold leading-tight">{{ skillsContent.title || 'Technical Attainments' }}</h3>
                </div>

                <div class="space-y-6">
                    <div
                        v-for="(skill, index) in skills"
                        :key="`${skill.title}-${index}`"
                        class="space-y-2"
                    >
                        <div class="flex items-center justify-between text-lg font-medium">
                            <p>{{ skill.title }}</p>
                            <p>{{ normalizeSkillValue(skill.value) }}%</p>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full border border-black/50 bg-[#ffe6a6]">
                            <div
                                class="h-full bg-black transition-all duration-700"
                                :style="{ width: `${normalizeSkillValue(skill.value)}%` }"
                            />
                        </div>
                    </div>
                </div>

                <button
                    v-if="nextSectionId('attainments')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('attainments')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('attainments') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="fun_facts"
                v-show="activeSection === 'fun_facts'"
                :aria-hidden="activeSection !== 'fun_facts'"
                :inert="activeSection !== 'fun_facts'"
                class="scroll-mt-28 pb-24"
            >
                <div class="mb-10">
                    <p class="text-[1.2rem] uppercase tracking-[0.14em] text-black/70">{{ factsContent.label || '05 Facts' }}</p>
                    <h3 class="mt-3 text-[clamp(2.05rem,4.1vw,3.85rem)] font-extrabold leading-tight">{{ factsContent.title || 'Fun Facts' }}</h3>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
                    <article
                        v-for="(fact, index) in facts"
                        :key="`${fact.label}-${index}`"
                        class="relative overflow-hidden rounded-3xl border border-black/50 bg-[#f1d368] p-6 text-center"
                    >
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-cyan-500 via-sky-500 to-indigo-500" />
                        <div class="mx-auto mb-5 inline-flex h-14 w-14 items-center justify-center rounded-full border border-black/35 bg-[#ffdb67]">
                            <AppIcon :name="factIcon(index)" class="h-6 w-6" />
                        </div>
                        <p class="text-[clamp(2rem,3.8vw,3.35rem)] font-extrabold leading-none">{{ fact.value }}</p>
                        <p class="mt-2 text-base md:text-lg">{{ fact.label }}</p>
                    </article>
                </div>

                <button
                    v-if="nextSectionId('fun_facts')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('fun_facts')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('fun_facts') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="experience"
                v-show="activeSection === 'experience'"
                :aria-hidden="activeSection !== 'experience'"
                :inert="activeSection !== 'experience'"
                class="scroll-mt-28 pb-24"
            >
                <div class="mb-10">
                    <p class="text-[1.2rem] uppercase tracking-[0.14em] text-black/70">{{ experienceContent.label || '06 Experiences' }}</p>
                    <h3 class="mt-3 text-[clamp(2.05rem,4.1vw,3.85rem)] font-extrabold leading-tight">{{ experienceContent.title || 'Professional Journey' }}</h3>
                </div>

                <div class="space-y-6 border-l-2 border-black/40 pl-6">
                    <article
                        v-for="(item, index) in timeline"
                        :key="`${item.period}-${index}`"
                        class="relative overflow-hidden rounded-3xl border border-black/40 bg-[#f1d368] p-5 md:p-6"
                    >
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-violet-500 via-fuchsia-500 to-rose-500" />
                        <span class="absolute -left-[34px] top-7 h-4 w-4 rounded-full border border-black bg-[#ffdb67]" />
                        <p class="flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.16em] text-black/70">
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-black/30 bg-[#ffdb67]">
                                <AppIcon :name="timelineIcon(index)" class="h-4 w-4" />
                            </span>
                            {{ item.period }}
                        </p>
                        <h4 class="mt-2 text-2xl font-semibold md:text-[1.9rem]">{{ item.title }}</h4>
                        <p class="mt-1 text-lg font-medium text-black/80">{{ item.company }}</p>
                        <p class="mt-3 text-base leading-relaxed md:text-lg">{{ item.desc }}</p>
                    </article>
                </div>

                <button
                    v-if="nextSectionId('experience')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('experience')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('experience') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="projects"
                v-show="activeSection === 'projects'"
                :aria-hidden="activeSection !== 'projects'"
                :inert="activeSection !== 'projects'"
                class="scroll-mt-28 pb-24"
            >
                <div class="mb-10">
                    <p class="text-[1.2rem] uppercase tracking-[0.14em] text-black/70">{{ projectsContent.label || '07 Projects' }}</p>
                    <h3 class="mt-3 text-[clamp(2.05rem,4.1vw,3.85rem)] font-extrabold leading-tight">{{ projectsContent.title || 'Selected Work' }}</h3>
                </div>

                <div class="grid gap-6 lg:grid-cols-2 2xl:grid-cols-3">
                    <PortfolioCard
                        v-for="(project, index) in projectItems"
                        :key="`${project.title}-${index}`"
                        :image="project.image"
                        :title="project.title"
                        :category="project.category"
                        :summary="project.summary"
                        :badge="project.badge || ''"
                        :tags="project.tags || []"
                    />
                </div>

                <button
                    v-if="nextSectionId('projects')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('projects')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('projects') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="certificates"
                v-show="activeSection === 'certificates'"
                :aria-hidden="activeSection !== 'certificates'"
                :inert="activeSection !== 'certificates'"
                class="scroll-mt-28 pb-24"
            >
                <div class="mb-10">
                    <p class="text-[1.2rem] uppercase tracking-[0.14em] text-black/70">{{ certificatesContent.label || '08 Certificates' }}</p>
                    <h3 class="mt-3 text-[clamp(2.05rem,4.1vw,3.85rem)] font-extrabold leading-tight">{{ certificatesContent.title || 'Certifications' }}</h3>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <article
                        v-for="(certificate, index) in certificateItems"
                        :key="`${certificate.title}-${index}`"
                        class="relative overflow-hidden rounded-3xl border border-black/45 bg-[#f1d368] p-5 md:p-6"
                    >
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500" />
                        <a
                            :href="toAssetHref(certificate.file || certificate.image)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="block overflow-hidden rounded-2xl border border-black/25"
                        >
                            <img
                                :src="toAssetHref(certificate.image)"
                                :alt="certificate.title || 'Certificate'"
                                class="h-[220px] w-full object-cover"
                            >
                        </a>
                        <h4 class="mt-4 flex items-center gap-2 text-xl font-semibold leading-tight md:text-2xl">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-black/30 bg-[#ffdb67]">
                                <AppIcon name="certificate" class="h-4 w-4" />
                            </span>
                            {{ certificate.title || 'Certificate' }}
                        </h4>
                        <p class="mt-1 text-base text-black/75">{{ certificate.issuer || 'Certification' }}</p>
                        <a
                            v-if="certificate.file"
                            :href="toAssetHref(certificate.file)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="mt-4 inline-flex rounded-full border border-black px-6 py-2 text-sm font-semibold uppercase tracking-[0.14em] transition hover:bg-black hover:text-[#ffdb67]"
                        >
                            Open Certificate
                        </a>
                    </article>
                </div>

                <button
                    v-if="nextSectionId('certificates')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('certificates')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('certificates') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="how_i_work"
                v-show="activeSection === 'how_i_work'"
                :aria-hidden="activeSection !== 'how_i_work'"
                :inert="activeSection !== 'how_i_work'"
                class="scroll-mt-28 pb-24"
            >
                <div class="mb-10">
                    <p class="text-[1.2rem] uppercase tracking-[0.14em] text-black/70">{{ processContent.label || '09 How I Work' }}</p>
                    <h3 class="mt-3 text-[clamp(2.05rem,4.1vw,3.85rem)] font-extrabold leading-tight">{{ processContent.title || 'Simple, Clear Process' }}</h3>
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <article
                        v-for="(step, index) in processSteps"
                        :key="`${step.title}-${index}`"
                        class="relative overflow-hidden rounded-3xl border border-black/50 bg-[#f1d368] p-6"
                    >
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-indigo-500 via-violet-500 to-purple-500" />
                        <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full border border-black/35 bg-[#ffdb67]">
                            <AppIcon :name="stepIcon(index)" class="h-5 w-5" />
                        </div>
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-black/70">{{ step.step || `Step ${index + 1}` }}</p>
                        <h4 class="mt-2 text-2xl font-semibold">{{ step.title }}</h4>
                        <p class="mt-3 text-base md:text-lg">{{ step.description }}</p>
                    </article>
                </div>

                <button
                    v-if="nextSectionId('how_i_work')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('how_i_work')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('how_i_work') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="blogs"
                v-show="activeSection === 'blogs'"
                :aria-hidden="activeSection !== 'blogs'"
                :inert="activeSection !== 'blogs'"
                class="scroll-mt-28 pb-24"
            >
                <div class="mb-10">
                    <p class="text-[1.2rem] uppercase tracking-[0.14em] text-black/70">{{ blogsContent.label || '10 Blogs' }}</p>
                    <h3 class="mt-3 text-[clamp(2.05rem,4.1vw,3.85rem)] font-extrabold leading-tight">{{ blogsContent.title || 'Latest Articles' }}</h3>
                </div>

                <div class="space-y-6">
                    <article
                        v-for="(blog, index) in blogCards"
                        :key="`${blog.title}-${index}`"
                        class="relative grid gap-6 overflow-hidden rounded-3xl border border-black/35 bg-[#f1d368] p-5 md:grid-cols-[250px_1fr]"
                    >
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-rose-500 via-pink-500 to-fuchsia-500" />
                        <img :src="blog.image" :alt="blog.title" class="h-full w-full rounded-2xl border border-black/25 object-cover">
                        <div>
                            <h4 class="text-[clamp(1.6rem,2.7vw,2.35rem)] font-semibold leading-tight">{{ blog.title }}</h4>
                            <p class="mt-2 flex items-center gap-2 text-base font-medium uppercase tracking-[0.12em] text-black/65">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-black/30 bg-[#ffdb67]">
                                    <AppIcon name="calendar" class="h-4 w-4" />
                                </span>
                                {{ blog.date }} &bull; {{ blog.time }}
                            </p>
                            <p class="mt-3 text-base leading-relaxed md:text-lg">{{ blog.summary }}</p>
                        </div>
                    </article>
                </div>

                <button
                    v-if="nextSectionId('blogs')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('blogs')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('blogs') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="testimonials"
                v-show="activeSection === 'testimonials'"
                :aria-hidden="activeSection !== 'testimonials'"
                :inert="activeSection !== 'testimonials'"
                class="scroll-mt-28 pb-24"
            >
                <div class="mb-10">
                    <p class="text-[1.2rem] uppercase tracking-[0.14em] text-black/70">{{ testimonialsContent.label || '11 Testimonials' }}</p>
                    <h3 class="mt-3 text-[clamp(2.05rem,4.1vw,3.85rem)] font-extrabold leading-tight">{{ testimonialsContent.title || 'What Clients Say' }}</h3>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <article
                        v-for="(item, index) in testimonials"
                        :key="`${item.author}-${index}`"
                        class="relative overflow-hidden rounded-3xl border border-black/45 bg-[#f1d368] p-5 md:p-6"
                    >
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-amber-500 via-orange-500 to-red-500" />
                        <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full border border-black/35 bg-[#ffdb67]">
                            <AppIcon name="quote" class="h-5 w-5" />
                        </div>
                        <p class="text-xl leading-relaxed md:text-[1.35rem]">&ldquo;{{ item.quote }}&rdquo;</p>
                        <div class="mt-6">
                            <p class="text-xl font-semibold">{{ item.author }}</p>
                            <p class="text-base text-black/75 md:text-lg">{{ item.role }}</p>
                        </div>
                    </article>
                </div>

                <button
                    v-if="nextSectionId('testimonials')"
                    type="button"
                    class="mt-14 flex items-center gap-4 text-[1.45rem] font-medium"
                    @click="openNextSection('testimonials')"
                >
                    <span class="text-[1.15rem] text-black/60">{{ sectionPosition('testimonials') }}</span>
                    <span>{{ nextChapterLabel }}</span>
                    <span class="text-2xl">&rarr;</span>
                </button>
            </section>

            <section
                id="contact"
                v-show="activeSection === 'contact'"
                :aria-hidden="activeSection !== 'contact'"
                :inert="activeSection !== 'contact'"
                class="scroll-mt-28 pb-20"
            >
                <div class="mb-10">
                    <p class="text-[1.2rem] uppercase tracking-[0.14em] text-black/70">{{ contactContent.label || '12 Contact' }}</p>
                    <h3 class="mt-3 text-[clamp(2.05rem,4.1vw,3.85rem)] font-extrabold leading-tight">{{ contactContent.title || "Let's Build Something Great" }}</h3>
                </div>

                <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                    <div class="relative space-y-4 overflow-hidden rounded-3xl border border-black/45 bg-[#f1d368] p-5 text-base md:p-6 md:text-lg">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-teal-500 via-cyan-500 to-sky-500" />
                        <p class="flex items-center gap-3 font-semibold">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-black/35 bg-[#ffdb67]">
                                <AppIcon name="phone" class="h-4 w-4" />
                            </span>
                            {{ contactContent.phone || '-' }}
                        </p>
                        <p class="flex items-center gap-3 font-semibold">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-black/35 bg-[#ffdb67]">
                                <AppIcon name="email" class="h-4 w-4" />
                            </span>
                            {{ contactContent.email || '-' }}
                        </p>
                        <p class="flex items-center gap-3 font-semibold">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-black/35 bg-[#ffdb67]">
                                <AppIcon name="location" class="h-4 w-4" />
                            </span>
                            {{ contactContent.location || '-' }}
                        </p>
                        <p class="pt-2 leading-relaxed text-black/80">
                            {{ contactContent.description || '' }}
                        </p>
                        <Link
                            :href="contactContent.hire_me_href || '/register'"
                            class="inline-flex rounded-full bg-black px-7 py-3 text-base font-semibold uppercase tracking-[0.14em] text-[#ffdb67] transition hover:bg-black/90"
                        >
                            {{ contactContent.hire_me_label || 'Hire Me' }}
                        </Link>
                    </div>

                    <form class="relative space-y-4 overflow-hidden rounded-3xl border border-black/45 bg-[#f1d368] p-5 md:p-6" @submit.prevent="onContactSubmit">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500" />
                        <h4 class="text-2xl font-semibold">{{ contactContent.form_title || 'Leave a Message' }}</h4>
                        <div class="grid gap-4 md:grid-cols-2">
                            <input
                                type="text"
                                required
                                placeholder="Your name"
                                class="rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-2.5 text-base focus:border-black focus:outline-none focus:ring-0"
                            >
                            <input
                                type="email"
                                required
                                placeholder="Your email"
                                class="rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-2.5 text-base focus:border-black focus:outline-none focus:ring-0"
                            >
                        </div>
                        <input
                            type="text"
                            placeholder="Project title"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-2.5 text-base focus:border-black focus:outline-none focus:ring-0"
                        >
                        <textarea
                            rows="5"
                            placeholder="Tell me about your project"
                            class="w-full rounded-2xl border border-black/45 bg-[#ffdb67] px-4 py-2.5 text-base focus:border-black focus:outline-none focus:ring-0"
                        />
                        <button
                            type="submit"
                            class="rounded-full border border-black px-7 py-3 text-base font-semibold uppercase tracking-[0.14em] transition hover:bg-black hover:text-[#ffdb67]"
                        >
                            {{ contactContent.submit_label || 'Send Message' }}
                        </button>
                        <p v-if="contactSubmitted" class="text-base font-medium text-emerald-800">
                            {{ contactContent.success_message || 'Message captured on frontend. Backend integration will be connected next.' }}
                        </p>
                    </form>
                </div>
            </section>
        </main>

        <Footer
            :brand="footerContent.brand || 'Portify'"
            :tagline="footerContent.tagline || 'Designed with Vue + Tailwind'"
        />
    </div>
</template>
