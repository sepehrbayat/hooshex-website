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

// Alpine.js Components
import loginModal from './components/loginModal.js';
import cartModal from './components/cartModal.js';

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
});
