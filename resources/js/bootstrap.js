import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF protection: use the XSRF-TOKEN cookie that Laravel sets on every response.
// This is more reliable than the meta tag because the cookie stays fresh even when
// the page has been open for a long time or the session gets regenerated.
window.axios.defaults.withCredentials = true;
window.axios.defaults.withXSRFToken = true;

// Fallback: also read from the meta tag for the initial page load.
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}
