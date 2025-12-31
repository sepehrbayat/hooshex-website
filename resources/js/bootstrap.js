import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Suppress Filament component loading errors that don't affect functionality
// These occur when Alpine tries to evaluate x-data before x-load scripts finish loading
const originalError = window.console.error;
window.console.error = function(...args) {
    const message = args[0]?.toString?.() || '';
    const fullMessage = args.join(' ');
    
    // UPDATED: Removed broad suppression of Livewire component errors
    // The root cause (race condition in modal unmount) has been fixed in 
    // resources/views/vendor/filament-actions/components/modals.blade.php
    
    // Only suppress known non-critical Filament form/table component loading errors
    // These are x-load timing issues where Alpine evaluates before component JS loads
    if (
        // Form component errors during x-load initialization
        message.includes('selectFormComponent is not defined') ||
        message.includes('textareaFormComponent is not defined') ||
        message.includes('colorPickerFormComponent is not defined') ||
        message.includes('dateTimePickerFormComponent is not defined') ||
        message.includes('fileUploadFormComponent is not defined') ||
        message.includes('keyValueFormComponent is not defined') ||
        message.includes('markdownEditorFormComponent is not defined') ||
        message.includes('richEditorFormComponent is not defined') ||
        message.includes('tagsInputFormComponent is not defined') ||
        // Table component errors during x-load initialization
        (message.includes('table is not defined') && !fullMessage.includes('Could not find Livewire component')) ||
        message.includes('selectedRecords is not defined') ||
        message.includes('isRecordSelected is not defined') ||
        message.includes('isGroupCollapsed is not defined')
    ) {
        return;
    }
    
    originalError.apply(window.console, args);
};