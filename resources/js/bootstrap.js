import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Attach the CSRF token to every Axios request so Laravel accepts it.
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
}
