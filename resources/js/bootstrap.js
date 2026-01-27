import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.defaults.baseURL = '/api';

// CSRF Token setup
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Axios interceptor untuk handle error global
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            // Handle 401 Unauthorized
            if (error.response.status === 401) {
                window.location.href = '/login';
            }
            
            // Handle 403 Forbidden
            if (error.response.status === 403) {
                alert('Anda tidak memiliki akses untuk melakukan tindakan ini.');
            }
            
            // Handle 500 Server Error
            if (error.response.status === 500) {
                console.error('Server Error:', error.response.data);
            }
        }
        
        return Promise.reject(error);
    }
);