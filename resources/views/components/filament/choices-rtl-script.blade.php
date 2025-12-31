<script>
    (function() {
        function fixChoicesSpacing() {
            // Find all Choices.js inner containers (note: double underscore)
            const choicesInners = document.querySelectorAll('.choices__inner, .choices_inner');
            
            if (choicesInners.length === 0) {
                return; // No Choices elements found yet
            }
            
            choicesInners.forEach(function(inner) {
                // Set padding-left directly via inline style (more space for icons)
                inner.style.setProperty('padding-left', '5.5rem', 'important');
                inner.style.setProperty('padding-right', '0.75rem', 'important');
                inner.style.setProperty('position', 'relative', 'important');
                inner.style.setProperty('min-height', '2.5rem', 'important');
                
                // Find and position X button (remove button) - try both underscore variants
                const removeButton = inner.querySelector('.choices__button, .choices_button');
                if (removeButton) {
                    removeButton.style.setProperty('position', 'absolute', 'important');
                    removeButton.style.setProperty('left', '0.75rem', 'important');
                    removeButton.style.setProperty('right', 'auto', 'important');
                    removeButton.style.setProperty('top', '50%', 'important');
                    removeButton.style.setProperty('transform', 'translateY(-50%)', 'important');
                    removeButton.style.setProperty('z-index', '50', 'important');
                    removeButton.style.setProperty('margin', '0', 'important');
                    removeButton.style.setProperty('padding', '0.25rem', 'important');
                    removeButton.style.setProperty('cursor', 'pointer', 'important');
                    removeButton.style.setProperty('width', '1.5rem', 'important');
                    removeButton.style.setProperty('height', '1.5rem', 'important');
                    removeButton.style.setProperty('display', 'flex', 'important');
                    removeButton.style.setProperty('align-items', 'center', 'important');
                    removeButton.style.setProperty('justify-content', 'center', 'important');
                    removeButton.style.setProperty('flex-shrink', '0', 'important');
                    removeButton.style.setProperty('color', '#EB55C8', 'important');
                    removeButton.style.setProperty('opacity', '1', 'important');
                    
                    // Make SVG inside visible
                    const svg = removeButton.querySelector('svg');
                    if (svg) {
                        svg.style.setProperty('stroke', '#EB55C8', 'important');
                        svg.style.setProperty('fill', '#EB55C8', 'important');
                        svg.style.setProperty('color', '#EB55C8', 'important');
                        const path = svg.querySelector('path');
                        if (path) {
                            path.style.setProperty('stroke', '#EB55C8', 'important');
                            path.style.setProperty('fill', '#EB55C8', 'important');
                        }
                    }
                }
                
                // Find and position dropdown arrow - try both underscore variants
                const parentChoices = inner.closest('.choices');
                if (parentChoices) {
                    const arrow = parentChoices.querySelector('.choices__arrow, .choices_arrow');
                    if (arrow) {
                        arrow.style.setProperty('position', 'absolute', 'important');
                        arrow.style.setProperty('left', '3rem', 'important'); // After X button with gap
                        arrow.style.setProperty('right', 'auto', 'important');
                        arrow.style.setProperty('top', '50%', 'important');
                        arrow.style.setProperty('transform', 'translateY(-50%)', 'important');
                        arrow.style.setProperty('z-index', '40', 'important');
                        arrow.style.setProperty('pointer-events', 'none', 'important');
                        arrow.style.setProperty('width', '1.5rem', 'important');
                        arrow.style.setProperty('height', '1.5rem', 'important');
                        arrow.style.setProperty('display', 'flex', 'important');
                        arrow.style.setProperty('align-items', 'center', 'important');
                        arrow.style.setProperty('justify-content', 'center', 'important');
                        arrow.style.setProperty('flex-shrink', '0', 'important');
                    }
                }
                
                // Align text to right and ensure proper overflow handling - try both underscore variants
                const items = inner.querySelectorAll('.choices__item, .choices_item');
                items.forEach(function(item) {
                    item.style.setProperty('text-align', 'right', 'important');
                    item.style.setProperty('padding-right', '0', 'important');
                    item.style.setProperty('padding-left', '0', 'important');
                    item.style.setProperty('width', '100%', 'important');
                    item.style.setProperty('overflow', 'hidden', 'important');
                    item.style.setProperty('text-overflow', 'ellipsis', 'important');
                    item.style.setProperty('white-space', 'nowrap', 'important');
                });
            });
        }
        
        // Run immediately if DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(fixChoicesSpacing, 100);
            });
        } else {
            fixChoicesSpacing();
        }
        
        // Run after delays (for dynamically loaded Choices)
        setTimeout(fixChoicesSpacing, 200);
        setTimeout(fixChoicesSpacing, 500);
        setTimeout(fixChoicesSpacing, 1000);
        setTimeout(fixChoicesSpacing, 2000);
        
        // Watch for new Choices instances (Livewire updates)
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                setTimeout(fixChoicesSpacing, 50);
            });
            
            if (document.body) {
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
        }
        
        // Also run after Livewire updates
        window.addEventListener('livewire:load', function() {
            setTimeout(fixChoicesSpacing, 200);
        });
        
        window.addEventListener('livewire:update', function() {
            setTimeout(fixChoicesSpacing, 200);
        });
        
        // Run periodically to catch any missed updates
        setInterval(fixChoicesSpacing, 2000);
    })();
</script>
