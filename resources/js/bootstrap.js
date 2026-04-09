import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

const appKey = import.meta.env.VITE_REVERB_APP_KEY;
const configuredHost = import.meta.env.VITE_REVERB_HOST;
const browserHost = window.location.hostname;

const wsHost = (() => {
    if (!configuredHost) {
        return browserHost;
    }

    if (
        ['127.0.0.1', 'localhost'].includes(configuredHost)
        && !['127.0.0.1', 'localhost'].includes(browserHost)
    ) {
        return browserHost;
    }

    return configuredHost;
})();

if (appKey) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: appKey,
        wsHost,
        wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 80),
        wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 443),
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
} else {
    window.Echo = null;
    console.warn('Realtime disabled: VITE_REVERB_APP_KEY is missing.');
}
