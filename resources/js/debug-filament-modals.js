/**
 * Filament Modal Debugging - Comprehensive Diagnostics
 * 
 * This script logs the exact sequence of events to diagnose the race condition
 * between Livewire component morphing and Alpine.js $wire cleanup in Filament modals.
 * 
 * USAGE: Temporarily add to resources/js/app.js for debugging
 */

if (typeof window !== 'undefined') {
    const DEBUG_ENABLED = true; // Set to false to disable logging

    const log = (...args) => {
        if (DEBUG_ENABLED) {
            console.log('[FILAMENT DEBUG]', new Date().toISOString().split('T')[1].split('.')[0], ...args);
        }
    };

    // Track Livewire v3 lifecycle events
    document.addEventListener('livewire:init', () => {
        log('✅ Livewire initialized');

        // Hook into Livewire v3 message lifecycle
        window.Livewire.hook('message.sent', (message, component) => {
            log('📤 Livewire message.sent:', {
                componentId: component.id,
                updates: message.updateQueue || []
            });
        });

        window.Livewire.hook('message.processed', (message, component) => {
            log('📥 Livewire message.processed:', {
                componentId: component.id,
                response: message.response?.effects || {}
            });
        });

        window.Livewire.hook('element.updated', (el, component) => {
            log('🔄 Livewire element.updated:', {
                componentId: component.id,
                element: el.tagName,
                wireId: el.getAttribute('wire:id')
            });
        });

        window.Livewire.hook('component.init', ({ component }) => {
            log('🆕 Livewire component.init:', component.id);
        });

        window.Livewire.hook('element.removed', ({ el, component }) => {
            log('❌ Livewire element.removed:', {
                componentId: component?.id,
                element: el.tagName,
                wireId: el.getAttribute('wire:id'),
                hasModalParent: !!el.closest('[data-fi-modal-id]')
            });
        });
    });

    // Track Alpine initialization
    document.addEventListener('alpine:init', () => {
        log('✅ Alpine initialized');
    });

    document.addEventListener('alpine:initializing', () => {
        log('⏳ Alpine initializing');
    });

    // Track Filament modal events
    document.addEventListener('modal-opened', (e) => {
        log('🚪 Modal opened:', e.detail);
    });

    document.addEventListener('modal-closed', (e) => {
        log('🚪 Modal closed:', e.detail);
        
        // Check if the modal container still exists in the DOM
        const modalId = e.detail?.id;
        if (modalId) {
            const modal = document.querySelector(`[data-fi-modal-id="${modalId}"]`);
            if (!modal) {
                log('⚠️ Modal DOM element not found after close event!');
            } else {
                // Check if parent Livewire component exists
                const livewireParent = modal.closest('[wire\\:id]');
                if (!livewireParent) {
                    log('⚠️ Livewire parent component not found! This will cause $wire.unmountTableAction to fail');
                } else {
                    log('✅ Livewire parent still exists:', livewireParent.getAttribute('wire:id'));
                }
            }
        }
    });

    document.addEventListener('opened-form-component-action-modal', (e) => {
        log('📝 Form component action modal opened:', e.detail);
    });

    document.addEventListener('closed-form-component-action-modal', (e) => {
        log('📝 Form component action modal closed:', e.detail);
    });

    document.addEventListener('close-modal', (e) => {
        log('🔔 close-modal event dispatched:', e.detail);
    });

    document.addEventListener('open-modal', (e) => {
        log('🔔 open-modal event dispatched:', e.detail);
    });

    // Intercept $wire access errors
    const originalError = window.console.error;
    window.console.error = function(...args) {
        const message = args[0]?.toString?.() || '';
        
        if (message.includes('Alpine Expression Error') || 
            message.includes('Could not find Livewire component')) {
            log('❌ ERROR CAUGHT:', {
                message: message,
                fullArgs: args,
                stack: new Error().stack
            });
        }
        
        originalError.apply(window.console, args);
    };

    log('🔍 Filament Modal Debugging initialized');
}
