/**
 * Main JavaScript Entry Point
 * 
 * This file imports and initializes all modules and components.
 * All business logic is split into separate modules for better maintainability.
 */

import './bootstrap';

// Modules
import { initSwipers } from './modules/swiper.js';
import { hxCart } from './modules/cart.js';
import { initModals } from './modules/modals.js';
import './modules/features.js';
// Removed: import './modules/filament-forms.js'; - Filament 3 auto-loads components via x-load directive

// Alpine.js Components
import loginModal from './components/loginModal.js';
import cartModal from './components/cartModal.js';

// Filament 3 auto-loads all form components via x-load directive
// No manual initialization needed

// Initialize Swiper sliders when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    initSwipers();
});

// Initialize modal event listeners
initModals();

// Register Alpine.js components
// With Livewire v3, Alpine.js is bundled with Livewire
// Make functions available globally as fallback (for x-data="loginModal()" syntax)
window.loginModal = loginModal;
window.cartModal = cartModal;

// Register with Alpine.data() as the primary method (recommended)
document.addEventListener('alpine:init', () => {
    window.Alpine.data('loginModal', loginModal);
    window.Alpine.data('cartModal', cartModal);
    
    // FIX: Suppress "Could not find Livewire component in DOM tree" errors
    // This is a known race condition in Filament/Livewire when modals close
    const originalErrorHandler = window.Alpine.onError || (() => {});
    window.Alpine.onError = (error, el, expression) => {
        // Suppress Livewire component not found errors (race condition during modal close)
        if (error && (
            error.message?.includes('Could not find Livewire component') ||
            error.toString?.()?.includes('Could not find Livewire component')
        )) {
            console.debug('[Alpine] Suppressed Livewire race condition error:', error.message);
            return; // Silently ignore
        }
        // Call original error handler for other errors
        originalErrorHandler(error, el, expression);
    };
});
