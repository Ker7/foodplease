import './bootstrap';
import 'htmx.org';
import Alpine from 'alpinejs';

// Initialize Alpine
window.Alpine = Alpine;
Alpine.start();

// Configure HTMX
document.addEventListener('DOMContentLoaded', function() {
    // Add CSRF token to all HTMX requests
    document.body.addEventListener('htmx:configRequest', function(evt) {
        evt.detail.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    });

    // Show loading indicator
    document.body.addEventListener('htmx:beforeRequest', function(evt) {
        document.getElementById('loading-indicator').style.display = 'block';
    });

    // Hide loading indicator
    document.body.addEventListener('htmx:afterRequest', function(evt) {
        document.getElementById('loading-indicator').style.display = 'none';
    });

    // Handle form validation errors
    document.body.addEventListener('htmx:responseError', function(evt) {
        if (evt.detail.xhr.status === 422) {
            const response = JSON.parse(evt.detail.xhr.responseText);
            if (response.errors) {
                // Handle validation errors
                Object.keys(response.errors).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('border-red-500');
                        // Remove existing error message
                        const existingError = input.parentNode.querySelector('.text-red-500');
                        if (existingError) existingError.remove();
                        // Add new error message
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'text-red-500 text-sm mt-1';
                        errorDiv.textContent = response.errors[field][0];
                        input.parentNode.appendChild(errorDiv);
                    }
                });
            }
        }
    });
});