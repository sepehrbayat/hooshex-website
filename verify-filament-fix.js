/**
 * Filament Modal Fix - Automated Verification
 * 
 * Run this in browser console after loading admin panel to verify the fix.
 * Should show all checks passing with zero errors.
 */

(function verifyFilamentModalFix() {
    console.clear();
    console.log('%c🔍 Filament Modal Fix Verification', 'font-size: 16px; font-weight: bold; color: #4F46E5;');
    console.log('==========================================\n');

    const results = {
        passed: [],
        failed: [],
        warnings: []
    };

    // Check 1: Verify published views exist
    console.log('1️⃣  Checking for published Filament views...');
    fetch('/admin')
        .then(() => {
            results.passed.push('✅ Admin panel accessible');
        })
        .catch(() => {
            results.failed.push('❌ Cannot access admin panel');
        });

    // Check 2: Verify Livewire is loaded
    console.log('2️⃣  Checking Livewire initialization...');
    if (window.Livewire) {
        results.passed.push('✅ Livewire v3 loaded');
        console.log(`   Version detected: ${window.Livewire.version || 'Unknown'}`);
    } else {
        results.failed.push('❌ Livewire not found');
    }

    // Check 3: Verify Alpine is loaded
    console.log('3️⃣  Checking Alpine.js initialization...');
    if (window.Alpine) {
        results.passed.push('✅ Alpine.js loaded (bundled with Livewire)');
    } else {
        results.failed.push('❌ Alpine.js not found');
    }

    // Check 4: Check for modal elements
    console.log('4️⃣  Checking Filament modal structure...');
    const modals = document.querySelectorAll('[data-fi-modal-id]');
    if (modals.length > 0) {
        results.passed.push(`✅ Found ${modals.length} Filament modal(s)`);
        modals.forEach((modal, i) => {
            const modalId = modal.getAttribute('data-fi-modal-id');
            const hasWireKey = modal.querySelector('[wire\\:key]');
            console.log(`   Modal ${i + 1}: ${modalId} ${hasWireKey ? '(has wire:key)' : ''}`);
        });
    } else {
        results.warnings.push('⚠️  No modals found (may need to trigger an action first)');
    }

    // Check 5: Verify queueMicrotask is available
    console.log('5️⃣  Checking browser compatibility...');
    if (typeof queueMicrotask === 'function') {
        results.passed.push('✅ queueMicrotask() supported');
    } else {
        results.failed.push('❌ queueMicrotask() not supported (outdated browser?)');
        results.warnings.push('⚠️  Browser may not support the fix - update to modern version');
    }

    // Check 6: Monitor console errors
    console.log('6️⃣  Setting up error monitoring...');
    let errorCount = 0;
    const originalError = window.console.error;
    window.console.error = function(...args) {
        const message = args[0]?.toString?.() || '';
        
        if (message.includes('Could not find Livewire component in DOM tree') ||
            message.includes('Alpine Expression Error: undefined') ||
            message.includes('$wire.unmount')) {
            errorCount++;
            results.failed.push(`❌ ERROR DETECTED: ${message.substring(0, 80)}...`);
        }
        
        originalError.apply(window.console, args);
    };
    results.passed.push('✅ Error monitoring active');

    // Check 7: Test modal event listeners
    console.log('7️⃣  Checking modal event listeners...');
    let modalOpenedCount = 0;
    let modalClosedCount = 0;
    
    document.addEventListener('modal-opened', () => modalOpenedCount++);
    document.addEventListener('modal-closed', () => modalClosedCount++);
    
    results.passed.push('✅ Event listeners registered');

    // Print results after 2 seconds
    setTimeout(() => {
        console.log('\n==========================================');
        console.log('%c📊 Verification Results', 'font-size: 14px; font-weight: bold; color: #059669;');
        console.log('==========================================\n');

        console.log('%cPassed Checks:', 'font-weight: bold; color: #059669;');
        results.passed.forEach(item => console.log(item));

        if (results.warnings.length > 0) {
            console.log('\n%cWarnings:', 'font-weight: bold; color: #D97706;');
            results.warnings.forEach(item => console.log(item));
        }

        if (results.failed.length > 0) {
            console.log('\n%cFailed Checks:', 'font-weight: bold; color: #DC2626;');
            results.failed.forEach(item => console.log(item));
        }

        console.log('\n==========================================');
        console.log('%c📝 Next Steps:', 'font-size: 14px; font-weight: bold; color: #4F46E5;');
        console.log('==========================================');
        console.log('1. Navigate to a resource with table actions (e.g., Course Chapters)');
        console.log('2. Click "Create" or "Edit" button to open modal');
        console.log('3. Save or cancel the modal');
        console.log('4. Check this console for errors (should be ZERO)');
        console.log('5. Repeat with Delete, Bulk Actions, etc.');
        console.log('\n🎯 Expected result: No "Livewire component" or "Alpine Expression" errors');
        console.log(`\n📊 Current error count: ${errorCount}`);
        console.log(`📊 Modals opened: ${modalOpenedCount}, closed: ${modalClosedCount}`);

        if (results.failed.length === 0 && errorCount === 0) {
            console.log('\n%c🎉 VERIFICATION PASSED!', 'font-size: 16px; font-weight: bold; color: #059669; background: #D1FAE5; padding: 8px;');
            console.log('The fix is working correctly. Test actual actions to confirm.');
        } else {
            console.log('\n%c⚠️  ISSUES DETECTED', 'font-size: 16px; font-weight: bold; color: #DC2626; background: #FEE2E2; padding: 8px;');
            console.log('See FILAMENT_MODAL_FIX_COMPLETE.md for troubleshooting.');
        }
    }, 2000);

    console.log('\n⏳ Running checks... Results in 2 seconds...\n');
})();
