/**
 * Filament Form Components Initializer
 * 
 * Ensures Filament form component JavaScript definitions are loaded
 * and Alpine.js component functions are registered before form components
 * try to use them.
 */

export function initFilamentForms() {
    // Wait for Alpine to be ready
    return new Promise((resolve) => {
        if (window.Alpine) {
            // Alpine already loaded
            resolve();
        } else {
            // Wait for Alpine to load
            document.addEventListener('alpine:initializing', () => {
                resolve();
            });
            // Fallback: resolve after a short timeout
            setTimeout(resolve, 1000);
        }
    }).then(() => {
        // Now load Filament form component scripts
        return loadFilamentFormComponents();
    });
}

function loadFilamentFormComponents() {
    const formComponentScripts = [
        '/js/filament/forms/components/select.js',
        '/js/filament/forms/components/textarea.js',
        '/js/filament/forms/components/color-picker.js',
        '/js/filament/forms/components/date-time-picker.js',
        '/js/filament/forms/components/file-upload.js',
        '/js/filament/forms/components/key-value.js',
        '/js/filament/forms/components/markdown-editor.js',
        '/js/filament/forms/components/rich-editor.js',
        '/js/filament/forms/components/tags-input.js',
        '/js/filament/tables/components/table.js', // Add table component
    ];

    const scripts = formComponentScripts.map((src) => {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.async = false;
            script.onload = resolve;
            script.onerror = () => {
                // Log but don't fail - some components may not be used
                console.warn(`Failed to load Filament form component: ${src}`);
                resolve();
            };
            document.head.appendChild(script);
        });
    });

    return Promise.all(scripts);
}

// Auto-initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFilamentForms);
} else {
    // DOM is already loaded
    initFilamentForms();
}
