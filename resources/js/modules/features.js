/**
 * Features Section Utilities
 * Equalizes heights of feature cards
 */

export function equalizeFeatureCardHeights() {
    const cards = document.querySelectorAll('.feature-card');
    
    if (cards.length === 0) {
        return;
    }

    // Reset heights first
    cards.forEach(card => {
        card.style.height = 'auto';
    });

    // Get all cards in the same row (for grid layout)
    const grid = document.querySelector('.features-grid');
    if (!grid) {
        return;
    }

    // For grid layout (mobile - 2 columns)
    if (window.getComputedStyle(grid).display === 'grid') {
        const rows = Math.ceil(cards.length / 2);
        
        for (let i = 0; i < rows; i++) {
            const rowCards = Array.from(cards).slice(i * 2, (i + 1) * 2);
            if (rowCards.length > 0) {
                const maxHeight = Math.max(...rowCards.map(card => card.offsetHeight));
                rowCards.forEach(card => {
                    card.style.height = `${maxHeight}px`;
                });
            }
        }
    } else {
        // For flexbox layout (tablet/desktop)
        const maxHeight = Math.max(...Array.from(cards).map(card => card.offsetHeight));
        cards.forEach(card => {
            card.style.height = `${maxHeight}px`;
        });
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    equalizeFeatureCardHeights();
    
    // Re-calculate on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            equalizeFeatureCardHeights();
        }, 250);
    });
});

// Also run after Livewire updates
document.addEventListener('livewire:load', () => {
    equalizeFeatureCardHeights();
});

document.addEventListener('livewire:update', () => {
    setTimeout(equalizeFeatureCardHeights, 100);
});

