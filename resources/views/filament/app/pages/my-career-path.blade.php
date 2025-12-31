<x-filament-panels::page>
    @push('styles')
    <style>
        /* RTL Direction */
        html, body {
            direction: rtl !important;
        }
        
        [dir] {
            direction: rtl !important;
        }
        
        /* Sidebar RTL - Prevent centering on all screen sizes */
        .fi-sidebar,
        .fi-main-sidebar {
            right: 0 !important;
            left: auto !important;
            border-left: 1px solid rgba(119, 95, 238, 0.1) !important;
            border-right: none !important;
            margin-right: 0 !important;
            margin-left: auto !important;
        }
        
        /* When sidebar is open, ensure it's at right: 0 and not centered */
        .fi-sidebar.fi-sidebar-open,
        .fi-sidebar[class*="translate-x-0"],
        .fi-sidebar.rtl\:-translate-x-0 {
            right: 0 !important;
            left: auto !important;
            transform: translateX(0) !important;
        }
        
        /* Ensure sidebar nav doesn't center */
        .fi-sidebar-nav {
            justify-content: flex-start !important;
            align-items: flex-start !important;
            text-align: right !important;
        }
        
        /* Mobile and tablet - prevent centering when open */
        @media (max-width: 1023px) {
            .fi-sidebar.fi-sidebar-open {
                right: 0 !important;
                left: auto !important;
                transform: translateX(0) !important;
                margin: 0 !important;
                position: fixed !important;
            }
        }
        
        /* Desktop - ensure sidebar stays on right */
        @media (min-width: 1024px) {
            .fi-sidebar {
                right: 0 !important;
                left: auto !important;
            }
        }
        
        /* Content RTL */
        .fi-main-content {
            margin-right: 0 !important;
            margin-left: auto !important;
        }
        
        /* Navigation RTL */
        .fi-sidebar-nav-item {
            text-align: right !important;
            padding-right: 1rem !important;
            padding-left: 0.5rem !important;
        }
        
        /* Form RTL */
        .fi-section-content-ctn {
            text-align: right !important;
        }
        
        /* Button Actions RTL */
        .fi-ac {
            justify-content: flex-start !important;
            flex-direction: row-reverse !important;
        }
        
        /* Tables RTL */
        .fi-ta-table {
            direction: rtl !important;
            text-align: right !important;
        }
        
        /* Input fields keep ltr for email/password */
        input[dir="ltr"],
        textarea[dir="ltr"] {
            direction: ltr !important;
            text-align: left !important;
        }
        
        /* Select/Dropdown Fields RTL - Fix arrow overlap */
        /* Filament Select Input Wrapper */
        .fi-input-wrp {
            position: relative !important;
        }
        
        /* Select input padding for RTL to prevent arrow overlap */
        .fi-input-wrp select,
        select.fi-input,
        .fi-fo-field-wrp-label-hint select,
        [data-field-wrapper] select {
            padding-right: 2.5rem !important;
            padding-left: 0.75rem !important;
            text-align: right !important;
            direction: rtl !important;
        }
        
        /* Filament Select Icon/Arrow positioning for RTL - Only for regular selects (not selected value) */
        .fi-input-wrp:not(.fi-select .fi-input-wrp) .fi-icon,
        .fi-input-wrp:not(.fi-select .fi-input-wrp) svg,
        .fi-input-wrp select + .fi-icon,
        .fi-input-wrp select + svg {
            left: 0.75rem !important;
            right: auto !important;
            position: absolute !important;
            pointer-events: none !important;
        }
        
        /* Pagination per page selector */
        .fi-pagination-per-page select,
        .fi-pagination select,
        select[class*="fi-"],
        .fi-pagination-per-page .fi-input-wrp select {
            padding-right: 2.5rem !important;
            padding-left: 0.75rem !important;
            text-align: right !important;
            direction: rtl !important;
        }
        
        /* Pagination per page wrapper icon positioning */
        .fi-pagination-per-page .fi-input-wrp .fi-icon,
        .fi-pagination-per-page .fi-input-wrp svg,
        .fi-pagination .fi-input-wrp svg {
            left: 0.75rem !important;
            right: auto !important;
            position: absolute !important;
        }
        
        /* Ensure icons don't overlap with text - Only for non-select fields */
        .fi-input-wrp:not(.fi-select .fi-input-wrp) > div[class*="icon"],
        .fi-input-wrp:not(.fi-select .fi-input-wrp) > svg:not([class*="remove"]):not([class*="close"]),
        .fi-input-wrp:not(.fi-select .fi-input-wrp) > [class*="chevron"],
        .fi-input-wrp:not(.fi-select .fi-input-wrp) > [class*="arrow"] {
            left: 0.75rem !important;
            right: auto !important;
            position: absolute !important;
            pointer-events: none !important;
        }
        
        /* Fix Choices.js Select dropdown spacing for RTL - High specificity selectors */
        /* Target with higher specificity */
        div.choices,
        div.choices.is-focused,
        div.choices.is-open,
        .fi-fo-field-wrp-label-hint div.choices,
        [data-field-wrapper] div.choices {
            position: relative !important;
        }
        
        /* Choices inner - override with very high specificity */
        div.choices__inner,
        div.choices.is-focused .choices__inner,
        div.choices.is-open .choices__inner,
        .fi-fo-field-wrp-label-hint div.choices__inner,
        [data-field-wrapper] div.choices__inner,
        .fi-input-wrp div.choices__inner {
            padding-right: 0.75rem !important;
            padding-left: 5.5rem !important; /* Space for icons: X button (0.75rem + 1.5rem width) + gap (0.5rem) + arrow (3rem + 1.5rem width) + gap (0.5rem) */
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            position: relative !important;
            min-height: 2.5rem !important;
        }
        
        /* Choices list padding */
        div.choices__list,
        div.choices__list--single {
            padding-left: 22px !important;
            padding-right: 22px !important;
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }
        
        /* Selected item - right align with proper spacing */
        .choices__list--single .choices__item,
        .choices__list[aria-expanded="false"] .choices__item,
        div.choices__list--single .choices__item {
            text-align: right !important;
            padding-right: 0 !important;
            padding-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }
        
        /* Remove button (X icon) - High specificity */
        button.choices__button,
        button.choices__button[aria-label],
        .choices__list--single .choices__item button.choices__button,
        div.choices__list--single .choices__item button.choices__button,
        .fi-input-wrp button.choices__button,
        [data-field-wrapper] button.choices__button {
            position: absolute !important;
            left: 0.75rem !important;
            right: auto !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 50 !important;
            margin: 0 !important;
            padding: 0.25rem !important;
            background: transparent !important;
            border: none !important;
            cursor: pointer !important;
            width: 1.5rem !important;
            height: 1.5rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            color: #EB55C8 !important;
            opacity: 1 !important;
        }
        
        /* X icon SVG inside button - make it visible */
        button.choices__button svg,
        button.choices__button path {
            stroke: #EB55C8 !important;
            fill: #EB55C8 !important;
            color: #EB55C8 !important;
        }
        
        button.choices__button:hover {
            color: #C842A8 !important;
        }
        
        button.choices__button:hover svg,
        button.choices__button:hover path {
            stroke: #C842A8 !important;
            fill: #C842A8 !important;
        }
        
        /* Dropdown arrow - High specificity */
        span.choices__arrow,
        div.choices span.choices__arrow,
        .choices[data-type*="select-one"] span.choices__arrow,
        .fi-input-wrp span.choices__arrow,
        [data-field-wrapper] span.choices__arrow {
            position: absolute !important;
            left: 3rem !important; /* After X button (0.75rem + 1.5rem width + 0.75rem gap) */
            right: auto !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            z-index: 40 !important;
            pointer-events: none !important;
            width: 1.5rem !important;
            height: 1.5rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }
        
        /* Choices list items - right align */
        .choices__list--dropdown .choices__item,
        .choices__list[aria-expanded] .choices__item {
            text-align: right !important;
            padding-right: 0.75rem !important;
        }
        
        /* Original Button Styles */
        /* Button Styles - مطابق با استایل اصلی وبسایت */
        .fi-btn-primary,
        button[type="submit"].fi-btn-primary,
        .fi-ac-action[data-color="primary"],
        button.fi-ac-action[type="submit"] {
            background: linear-gradient(135deg, #775FEE 0%, #5537EA 100%) !important;
            box-shadow: 0px 2px 8px rgba(235, 85, 200, 0.46), inset 0px -4px 16px rgba(102, 24, 84, 0.13), inset 0px 4px 16px rgba(102, 24, 84, 0.11) !important;
            border-radius: 8px !important;
            font-family: 'Vazirmatn', sans-serif !important;
            font-weight: 600 !important;
            color: #FFFFFF !important;
            border: none !important;
            transition: all 0.3s ease !important;
        }

        .fi-btn-primary:hover,
        button[type="submit"].fi-btn-primary:hover,
        .fi-ac-action[data-color="primary"]:hover,
        button.fi-ac-action[type="submit"]:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0px 4px 12px rgba(235, 85, 200, 0.6), inset 0px -4px 16px rgba(102, 24, 84, 0.13), inset 0px 4px 16px rgba(102, 24, 84, 0.11) !important;
            color: #FFFFFF !important;
            background: linear-gradient(135deg, #5537EA 0%, #442CBB 100%) !important;
        }

        .fi-btn-primary:focus,
        button[type="submit"].fi-btn-primary:focus,
        .fi-ac-action[data-color="primary"]:focus,
        button.fi-ac-action[type="submit"]:focus {
            color: #FFFFFF !important;
            outline: 2px solid rgba(119, 95, 238, 0.3) !important;
            outline-offset: 2px !important;
        }

        .fi-btn-primary:active,
        button[type="submit"].fi-btn-primary:active,
        .fi-ac-action[data-color="primary"]:active,
        button.fi-ac-action[type="submit"]:active {
            color: #FFFFFF !important;
            transform: translateY(0) !important;
        }

        /* Ensure text color is white on all states */
        .fi-btn-primary *,
        .fi-btn-primary span,
        button[type="submit"].fi-btn-primary *,
        button[type="submit"].fi-btn-primary span,
        .fi-ac-action[data-color="primary"] *,
        .fi-ac-action[data-color="primary"] span {
            color: #FFFFFF !important;
        }
    </style>
    @endpush

    <script>
        (function() {
            function fixChoicesSpacing() {
                // Find all Choices.js inner containers (try both underscore variants)
                const choicesInners = document.querySelectorAll('.choices__inner, .choices_inner');
                
                if (choicesInners.length === 0) {
                    return;
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
            
            // Run after delays
            setTimeout(fixChoicesSpacing, 200);
            setTimeout(fixChoicesSpacing, 500);
            setTimeout(fixChoicesSpacing, 1000);
            setTimeout(fixChoicesSpacing, 2000);
            
            // Watch for DOM changes
            if (typeof MutationObserver !== 'undefined' && document.body) {
                const observer = new MutationObserver(function() {
                    setTimeout(fixChoicesSpacing, 50);
                });
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }
            
            // Run periodically as fallback
            setInterval(fixChoicesSpacing, 2000);
        })();
    </script>

    <form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </form>

    @php
        $selectedCareer = $this->selectedCareer;
    @endphp

    @if($selectedCareer)
        <x-filament::section>
            <x-slot name="heading">
                مسیر شغلی انتخاب شده
            </x-slot>

            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-semibold">{{ $selectedCareer->title }}</h3>
                    @if($selectedCareer->short_description)
                        <p class="text-gray-600 mt-2">{{ $selectedCareer->short_description }}</p>
                    @endif
                </div>

                @if($selectedCareer->location)
                    <div>
                        <span class="font-medium">موقعیت: </span>
                        <span>{{ $selectedCareer->location }}</span>
                    </div>
                @endif

                @if($selectedCareer->department)
                    <div>
                        <span class="font-medium">بخش: </span>
                        <span>{{ $selectedCareer->department }}</span>
                    </div>
                @endif

                <div>
                    <a href="{{ route('careers.show', $selectedCareer->slug) }}" 
                       target="_blank"
                       class="text-primary-600 hover:text-primary-700">
                        مشاهده جزئیات بیشتر →
                    </a>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>

