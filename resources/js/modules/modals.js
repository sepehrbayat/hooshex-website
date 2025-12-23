import { hxCart } from './cart.js';

/**
 * Modal management utilities
 */

export const openLoginModal = (next = null) => {
    window.dispatchEvent(new CustomEvent('login:open', { detail: { next } }));
};

/**
 * Initialize modal event listeners
 */
export function initModals() {
    document.addEventListener('click', (event) => {
        const loginTrigger = event.target.closest('[data-action="open-login"]');
        if (loginTrigger) {
            event.preventDefault();
            openLoginModal(loginTrigger.dataset.next ?? null);
            return;
        }

        const cartTrigger = event.target.closest('[data-action="open-cart"]');
        if (cartTrigger) {
            event.preventDefault();
            const isAuthenticated = Boolean(window.App?.isAuthenticated);
            if (isAuthenticated) {
                hxCart.openCart();
            } else {
                openLoginModal('cart');
            }
            return;
        }

        const profileTrigger = event.target.closest('[data-action="open-profile"]');
        if (profileTrigger) {
            event.preventDefault();
            const isAuthenticated = Boolean(window.App?.isAuthenticated);
            if (isAuthenticated) {
                window.dispatchEvent(new CustomEvent('profile:toggle'));
            } else {
                openLoginModal();
            }
        }
    });
}

