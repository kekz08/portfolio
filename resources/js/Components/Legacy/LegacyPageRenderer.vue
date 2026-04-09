<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    source: {
        type: String,
        required: true,
    },
    pageKey: {
        type: String,
        required: true,
    },
    routeMap: {
        type: Object,
        default: () => ({}),
    },
    forceHireMeCta: {
        type: Boolean,
        default: false,
    },
});

const renderedHtml = ref('');
const loading = ref(true);
const error = ref('');
const renderContainer = ref(null);

const injectedAssetNodes = [];
const injectedRuntimeScriptNodes = [];
const activeTypewriterTimeouts = new Set();
const interactionTeardowns = [];

const sourceBasePath = computed(() => {
    const pathname = new URL(props.source, window.location.origin).pathname;
    return pathname.replace(/[^/]+$/, '');
});

const absoluteAssetPath = (value) => {
    if (!value) {
        return value;
    }

    if (
        value.startsWith('http://')
        || value.startsWith('https://')
        || value.startsWith('//')
        || value.startsWith('data:')
        || value.startsWith('mailto:')
        || value.startsWith('tel:')
        || value.startsWith('#')
    ) {
        return value;
    }

    return new URL(value, `${window.location.origin}${sourceBasePath.value}`).pathname;
};

const normalizeRouteKey = (href) => {
    const trimmed = href.trim();

    if (!trimmed || trimmed === '/') {
        return '';
    }

    const absolutePath = new URL(trimmed, `${window.location.origin}${sourceBasePath.value}`).pathname;

    return absolutePath
        .replace(/^\/legacy\//, '')
        .replace(/^\/+/, '')
        .replace(/\/$/, '');
};

const rewriteAnchorHref = (anchor) => {
    const href = anchor.getAttribute('href');
    if (!href) {
        return;
    }

    if (props.forceHireMeCta && anchor.classList.contains('contact-btn')) {
        anchor.setAttribute('href', '/hire-me');
        anchor.textContent = ' hire me ';
        return;
    }

    if (href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
        return;
    }

    const [pathPart, hashPart = ''] = href.split('#');
    const key = normalizeRouteKey(pathPart);

    if (props.routeMap[key]) {
        anchor.setAttribute('href', `${props.routeMap[key]}${hashPart ? `#${hashPart}` : ''}`);
        return;
    }

    const absolute = absoluteAssetPath(pathPart);

    if (absolute.endsWith('/index.html')) {
        anchor.setAttribute(
            'href',
            `${absolute.replace(/\/index\.html$/, '') || '/'}${hashPart ? `#${hashPart}` : ''}`,
        );
        return;
    }

    anchor.setAttribute('href', `${absolute}${hashPart ? `#${hashPart}` : ''}`);
};

const removeInjectedAssets = () => {
    injectedAssetNodes.forEach((node) => node.remove());
    injectedAssetNodes.length = 0;
};

const removeInjectedRuntimeScripts = () => {
    injectedRuntimeScriptNodes.forEach((node) => node.remove());
    injectedRuntimeScriptNodes.length = 0;
};

const clearTypewriterTimeouts = () => {
    activeTypewriterTimeouts.forEach((timeoutId) => window.clearTimeout(timeoutId));
    activeTypewriterTimeouts.clear();
};

const clearInteractionBindings = () => {
    while (interactionTeardowns.length > 0) {
        const teardown = interactionTeardowns.pop();
        if (typeof teardown === 'function') {
            teardown();
        }
    }
};

const scheduleTypewriterTimeout = (callback, delay) => {
    const timeoutId = window.setTimeout(() => {
        activeTypewriterTimeouts.delete(timeoutId);
        callback();
    }, delay);

    activeTypewriterTimeouts.add(timeoutId);
};

const rewriteCssUrls = (cssContent) => cssContent.replace(
    /url\(\s*(['"]?)(.*?)\1\s*\)/g,
    (match, quote, rawUrl) => {
        const value = rawUrl.trim();

        if (
            !value
            || value.startsWith('data:')
            || value.startsWith('http://')
            || value.startsWith('https://')
            || value.startsWith('//')
            || value.startsWith('#')
        ) {
            return match;
        }

        const resolved = absoluteAssetPath(value);

        return `url(${quote}${resolved}${quote})`;
    },
);

const injectAssets = (documentNode) => {
    removeInjectedAssets();

    const head = document.head;
    const assets = documentNode.querySelectorAll('head style, head link[rel="stylesheet"]');

    assets.forEach((asset, index) => {
        if (asset.tagName.toLowerCase() === 'style') {
            const styleNode = document.createElement('style');
            styleNode.setAttribute('data-legacy-page', props.pageKey);
            styleNode.setAttribute('data-legacy-index', String(index));
            styleNode.textContent = rewriteCssUrls(asset.textContent ?? '');
            head.appendChild(styleNode);
            injectedAssetNodes.push(styleNode);
            return;
        }

        const href = asset.getAttribute('href');
        if (!href) {
            return;
        }

        const linkNode = document.createElement('link');
        linkNode.setAttribute('rel', 'stylesheet');
        linkNode.setAttribute('crossorigin', '');
        linkNode.setAttribute('data-legacy-page', props.pageKey);
        linkNode.setAttribute('data-legacy-index', String(index));
        linkNode.setAttribute('href', absoluteAssetPath(href));
        head.appendChild(linkNode);
        injectedAssetNodes.push(linkNode);
    });

    const safetyStyleNode = document.createElement('style');
    safetyStyleNode.setAttribute('data-legacy-page', props.pageKey);
    safetyStyleNode.setAttribute('data-legacy-index', 'safety');
    safetyStyleNode.textContent = `
        .loader-container{display:none !important;}
        header.header img,
        header.header-blog img,
        nav.navigation > img{
            width: clamp(120px, 10vw, 180px) !important;
            height: auto !important;
            max-width: 100% !important;
            object-fit: contain !important;
        }
    `;
    head.appendChild(safetyStyleNode);
    injectedAssetNodes.push(safetyStyleNode);
};

const rewriteLegacyMarkup = (documentNode) => {
    const root = documentNode.querySelector('#__nuxt');
    if (!root) {
        throw new Error('Legacy content root (#__nuxt) not found.');
    }

    // Static snapshots include Nuxt's fullscreen loader overlay; remove it
    // because no Nuxt runtime is mounted to dismiss it.
    root.querySelectorAll('.loader-container').forEach((element) => element.remove());

    root.querySelectorAll('[src]').forEach((element) => {
        const src = element.getAttribute('src');
        if (!src) {
            return;
        }
        element.setAttribute('src', absoluteAssetPath(src));
    });

    root.querySelectorAll('[href]').forEach((element) => {
        if (element.tagName.toLowerCase() === 'a') {
            rewriteAnchorHref(element);
            return;
        }

        const href = element.getAttribute('href');
        if (!href) {
            return;
        }

        if (href.startsWith('#') || href.startsWith('http://') || href.startsWith('https://') || href.startsWith('//')) {
            return;
        }

        element.setAttribute('href', absoluteAssetPath(href));
    });

    const teleportsNode = documentNode.querySelector('#teleports');
    const nuxtDataNode = documentNode.querySelector('#__NUXT_DATA__');

    return `${root.outerHTML}${teleportsNode ? teleportsNode.outerHTML : '<div id="teleports"></div>'}${nuxtDataNode ? nuxtDataNode.outerHTML : ''}`;
};

const injectLegacyRuntime = (documentNode) => {
    removeInjectedRuntimeScripts();

    const runtimeScript = documentNode.querySelector('script[type="module"][src]');
    if (!runtimeScript) {
        return false;
    }

    const runtimeSource = runtimeScript.getAttribute('src');
    if (!runtimeSource) {
        return false;
    }

    const scriptNode = document.createElement('script');
    scriptNode.type = 'module';
    scriptNode.src = absoluteAssetPath(runtimeSource);

    if (runtimeScript.hasAttribute('crossorigin')) {
        scriptNode.setAttribute('crossorigin', runtimeScript.getAttribute('crossorigin') ?? '');
    }

    document.body.appendChild(scriptNode);
    injectedRuntimeScriptNodes.push(scriptNode);

    return true;
};

const enforceHireMeCta = () => {
    if (!props.forceHireMeCta) {
        return;
    }

    const root = renderContainer.value;
    if (!root) {
        return;
    }

    root.querySelectorAll('a.contact-btn').forEach((anchor) => {
        anchor.setAttribute('href', '/hire-me');
        anchor.textContent = ' hire me ';
    });
};

const initializeHireMeObserver = () => {
    if (!props.forceHireMeCta) {
        return;
    }

    const root = renderContainer.value;
    if (!root) {
        return;
    }

    enforceHireMeCta();

    const observer = new MutationObserver(() => {
        enforceHireMeCta();
    });

    observer.observe(root, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['href', 'class'],
    });

    interactionTeardowns.push(() => observer.disconnect());
};

const initializeTypewriterFallback = () => {
    clearTypewriterTimeouts();

    const root = renderContainer.value;
    if (!root) {
        return;
    }

    const typedTargets = root.querySelectorAll('.typed-text');
    if (!typedTargets.length) {
        return;
    }

    const words = ['Developer', 'Designer'];
    const typeDelay = 120;
    const deleteDelay = 30;
    const holdDelay = 1500;

    typedTargets.forEach((typedTarget) => {
        const typewriterRoot = typedTarget.closest('.typewriter');
        const cursor = typewriterRoot ? typewriterRoot.querySelector('.cursor') : null;

        let wordIndex = 0;
        let charIndex = 0;

        const setTypingState = (isTyping) => {
            if (cursor) {
                cursor.classList.toggle('typing', isTyping);
            }
        };

        const typeNextCharacter = () => {
            const currentWord = words[wordIndex];

            if (charIndex < currentWord.length) {
                setTypingState(true);
                typedTarget.textContent += currentWord.charAt(charIndex);
                charIndex += 1;
                scheduleTypewriterTimeout(typeNextCharacter, typeDelay);
                return;
            }

            setTypingState(false);
            scheduleTypewriterTimeout(deleteCharacter, holdDelay);
        };

        const deleteCharacter = () => {
            const currentWord = words[wordIndex];

            if (charIndex > 0) {
                setTypingState(true);
                typedTarget.textContent = currentWord.substring(0, charIndex - 1);
                charIndex -= 1;
                scheduleTypewriterTimeout(deleteCharacter, deleteDelay);
                return;
            }

            setTypingState(false);
            wordIndex = (wordIndex + 1) % words.length;
            scheduleTypewriterTimeout(typeNextCharacter, typeDelay + 1000);
        };

        typedTarget.textContent = '';
        scheduleTypewriterTimeout(typeNextCharacter, holdDelay + 200);
    });
};

const initializeNavigationFallback = () => {
    const root = renderContainer.value;
    if (!root) {
        return;
    }

    const navRoot = root.querySelector('.navigation');
    if (!navRoot) {
        return;
    }

    const navLinks = Array.from(navRoot.querySelectorAll('ul li a[href^="#"]'));
    if (!navLinks.length) {
        return;
    }

    const getHashId = (value) => value.replace(/^#/, '').trim();
    const findSectionById = (id) => {
        if (!id) {
            return null;
        }

        // Avoid relying on CSS.escape so this works consistently across browsers.
        const exactMatch = root.querySelector(`[id="${id}"]`);
        if (exactMatch) {
            return exactMatch;
        }

        const globalMatch = document.getElementById(id);
        if (globalMatch && root.contains(globalMatch)) {
            return globalMatch;
        }

        return null;
    };
    const sectionEntries = navLinks
        .map((link) => {
            const href = link.getAttribute('href') ?? '';
            const id = getHashId(href);
            if (!id) {
                return null;
            }

            return { id, link, section: findSectionById(id) };
        })
        .filter(Boolean);

    if (!sectionEntries.length) {
        return;
    }

    const setActive = (id) => {
        sectionEntries.forEach(({ id: sectionId, link }) => {
            link.classList.toggle('active', sectionId === id);
        });
    };

    const syncFromHash = () => {
        const hashId = getHashId(window.location.hash);
        if (!hashId) {
            return;
        }

        const matchingEntry = sectionEntries.find(({ id }) => id === hashId);
        if (matchingEntry) {
            setActive(matchingEntry.id);
        }
    };

    const getScrollContainer = () => root.querySelector('[data-bs-spy="scroll"]');

    const updateActiveFromScroll = () => {
        const entriesWithSections = sectionEntries.filter(({ section }) => section);
        if (!entriesWithSections.length) {
            return;
        }

        const scrollContainer = getScrollContainer();
        const containerRectTop = scrollContainer ? scrollContainer.getBoundingClientRect().top : 0;
        const threshold = (scrollContainer ? scrollContainer.clientHeight : window.innerHeight) * 0.35;
        let currentId = entriesWithSections[0].id;

        entriesWithSections.forEach(({ id, section }) => {
            const rect = section.getBoundingClientRect();
            if (rect.top - containerRectTop <= threshold) {
                currentId = id;
            }
        });

        setActive(currentId);
    };

    const onHashChange = () => {
        syncFromHash();
        updateActiveFromScroll();
    };

    const onScroll = () => {
        updateActiveFromScroll();
    };

    navLinks.forEach((link) => {
        const onClick = (event) => {
            const href = link.getAttribute('href') ?? '';
            const id = getHashId(href);
            if (!id) {
                return;
            }

            const matchingEntry = sectionEntries.find((entry) => entry.id === id);
            event.preventDefault();
            setActive(id);
            if (matchingEntry?.section) {
                matchingEntry.section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            window.history.replaceState(null, '', `#${id}`);
        };

        link.addEventListener('click', onClick);
        interactionTeardowns.push(() => link.removeEventListener('click', onClick));
    });

    window.addEventListener('hashchange', onHashChange);
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);
    const scrollContainer = getScrollContainer();
    if (scrollContainer) {
        scrollContainer.addEventListener('scroll', onScroll, { passive: true });
        interactionTeardowns.push(() => scrollContainer.removeEventListener('scroll', onScroll));
    }

    interactionTeardowns.push(() => window.removeEventListener('hashchange', onHashChange));
    interactionTeardowns.push(() => window.removeEventListener('scroll', onScroll));
    interactionTeardowns.push(() => window.removeEventListener('resize', onScroll));

    // Legacy runtime can briefly re-hydrate and reset classes to "top".
    // Keep hash-based active state enforced for a short warmup window.
    const hashSyncIntervalId = window.setInterval(syncFromHash, 400);
    const hashSyncTimeoutId = window.setTimeout(() => {
        window.clearInterval(hashSyncIntervalId);
    }, 6000);
    interactionTeardowns.push(() => window.clearInterval(hashSyncIntervalId));
    interactionTeardowns.push(() => window.clearTimeout(hashSyncTimeoutId));

    const initialHashId = getHashId(window.location.hash);
    if (initialHashId) {
        const initialEntry = sectionEntries.find(({ id }) => id === initialHashId);
        if (initialEntry) {
            setActive(initialHashId);
            if (initialEntry.section) {
                initialEntry.section.scrollIntoView({ behavior: 'auto', block: 'start' });
            }
            return;
        }
    }

    updateActiveFromScroll();
};

const loadLegacyPage = async () => {
    loading.value = true;
    error.value = '';
    clearInteractionBindings();
    clearTypewriterTimeouts();
    removeInjectedRuntimeScripts();

    try {
        const response = await fetch(props.source, { credentials: 'same-origin' });
        if (!response.ok) {
            throw new Error(`Failed to load legacy source: ${response.status}`);
        }

        const html = await response.text();
        const parsed = new DOMParser().parseFromString(html, 'text/html');

        injectAssets(parsed);
        renderedHtml.value = rewriteLegacyMarkup(parsed);
        await nextTick();
        const runtimeInjected = injectLegacyRuntime(parsed);
        initializeHireMeObserver();
        initializeTypewriterFallback();
        initializeNavigationFallback();

        // Nuxt runtime can re-hydrate shortly after mount and reset nav classes.
        // Re-apply active-link sync so hash-based sections stay highlighted correctly.
        if (runtimeInjected) {
            scheduleTypewriterTimeout(initializeNavigationFallback, 300);
            scheduleTypewriterTimeout(initializeNavigationFallback, 1000);
            scheduleTypewriterTimeout(initializeNavigationFallback, 2500);
        }
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Unknown legacy rendering error.';
        renderedHtml.value = '';
    } finally {
        loading.value = false;
    }
};

onMounted(loadLegacyPage);
onBeforeUnmount(() => {
    clearInteractionBindings();
    clearTypewriterTimeouts();
    removeInjectedRuntimeScripts();
    removeInjectedAssets();
});
</script>

<template>
    <div v-if="loading" class="flex min-h-[50vh] items-center justify-center bg-black text-white">
        Loading portfolio...
    </div>

    <div v-else-if="error" class="mx-auto max-w-3xl rounded-md border border-red-200 bg-red-50 p-4 text-red-700">
        {{ error }}
    </div>

    <div
        v-else
        ref="renderContainer"
        class="legacy-portfolio-render"
        v-html="renderedHtml"
    />
</template>
