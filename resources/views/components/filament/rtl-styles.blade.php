<style>
    /* RTL Direction for Filament App Panel */
    html, body {
        direction: rtl !important;
    }
    
    [dir] {
        direction: rtl !important;
    }
    
    /* Sidebar RTL - Base positioning, always on right side */
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
    
    /* Content Area RTL */
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
    
    .fi-sidebar-nav-item-icon {
        margin-left: 0.75rem !important;
        margin-right: 0 !important;
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
    
    .fi-ta-header-cell,
    .fi-ta-cell {
        text-align: right !important;
    }
    
    /* Topbar RTL */
    .fi-topbar {
        text-align: right !important;
    }
    
    .fi-topbar-nav {
        flex-direction: row-reverse !important;
    }
    
    /* Input fields keep ltr for email/password */
    input[dir="ltr"],
    textarea[dir="ltr"] {
        direction: ltr !important;
        text-align: left !important;
    }
    
    /* Labels RTL */
    .fi-label {
        text-align: right !important;
    }
    
    /* Badges RTL */
    .fi-badge {
        text-align: right !important;
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
    
    /* Filament Select Icon/Arrow positioning for RTL */
    .fi-input-wrp .fi-icon,
    .fi-input-wrp-icon,
    .fi-input-wrp .fi-icon-btn,
    .fi-input-wrp svg,
    .fi-input-wrp > svg {
        left: 0.75rem !important;
        right: auto !important;
        position: absolute !important;
        pointer-events: none !important;
    }
    
    /* Specific for select arrow icons - more specific selectors */
    .fi-input-wrp svg.fi-icon,
    .fi-input-wrp .heroicon,
    .fi-input-wrp [x-data*="select"] svg,
    .fi-input-wrp > div > svg {
        left: 0.75rem !important;
        right: auto !important;
        pointer-events: none !important;
        position: absolute !important;
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
    
    /* Ensure icons don't overlap with text - general rule */
    .fi-input-wrp > div[class*="icon"],
    .fi-input-wrp > svg,
    .fi-input-wrp > [class*="chevron"],
    .fi-input-wrp > [class*="arrow"],
    .fi-input-wrp [class*="icon"] {
        left: 0.75rem !important;
        right: auto !important;
        position: absolute !important;
        pointer-events: none !important;
    }
    
    /* More specific selectors for Filament Select components */
    .fi-select .fi-input-wrp,
    .fi-fo-field-wrp .fi-input-wrp {
        position: relative !important;
    }
    
    /* Filament Select component specific */
    [data-field-wrapper] .fi-input-wrp select,
    .fi-fo-field-wrp-label-hint .fi-input-wrp select,
    .fi-select .fi-input-wrp select {
        padding-right: 2.5rem !important;
        padding-left: 0.75rem !important;
    }
    
    [data-field-wrapper] .fi-input-wrp svg,
    .fi-fo-field-wrp-label-hint .fi-input-wrp svg,
    .fi-select .fi-input-wrp svg,
    [data-field-wrapper] .fi-input-wrp .fi-icon,
    .fi-fo-field-wrp-label-hint .fi-input-wrp .fi-icon,
    .fi-select .fi-input-wrp .fi-icon {
        left: 0.75rem !important;
        right: auto !important;
        position: absolute !important;
        pointer-events: none !important;
    }
    
    /* Choices.js RTL - Fix dropdown arrow and clear button overlap */
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
    
    /* Pagination per page - more specific */
    .fi-pagination-per-page .fi-input-wrp,
    .fi-pg-per-page .fi-input-wrp {
        position: relative !important;
    }
    
    .fi-pagination-per-page .fi-input-wrp select,
    .fi-pg-per-page .fi-input-wrp select,
    .fi-pagination select {
        padding-right: 2.5rem !important;
        padding-left: 0.75rem !important;
        text-align: right !important;
        direction: rtl !important;
    }
    
    .fi-pagination-per-page .fi-input-wrp svg,
    .fi-pg-per-page .fi-input-wrp svg,
    .fi-pagination .fi-input-wrp svg,
    .fi-pagination-per-page .fi-input-wrp .fi-icon,
    .fi-pg-per-page .fi-input-wrp .fi-icon {
        left: 0.75rem !important;
        right: auto !important;
        position: absolute !important;
        pointer-events: none !important;
        z-index: 1 !important;
    }
    
    /* Filament Select Component with Clear Button - More Specific */
    .fi-select-wrapper,
    .fi-select-component,
    [x-data*="select"] {
        position: relative !important;
    }
    
    /* Select Input Field with Clear Button - RTL spacing */
    .fi-select input[type="text"],
    .fi-select input[type="search"],
    .fi-select .fi-input,
    [x-data*="select"] input,
    .fi-fo-field-wrp input[type="text"][readonly],
    .fi-fo-field-wrp .fi-input[readonly] {
        padding-right: 3rem !important; /* Space for clear button */
        padding-left: 2.5rem !important; /* Space for dropdown arrow */
        text-align: right !important;
        direction: rtl !important;
    }
    
    /* Clear Button (X icon) positioning in RTL */
    .fi-select .fi-icon-btn,
    .fi-select button[type="button"],
    [x-data*="select"] button,
    .fi-select [aria-label*="clear"],
    .fi-select [aria-label*="Clear"],
    .fi-input-wrp button,
    .fi-input-wrp .fi-icon-btn {
        right: 0.5rem !important;
        left: auto !important;
        position: absolute !important;
        z-index: 2 !important;
    }
    
    /* Dropdown Arrow (Chevron) positioning in RTL - Left side */
    .fi-select .fi-icon:not(.fi-icon-btn),
    .fi-select svg:not(button svg),
    [x-data*="select"] svg:not(button svg),
    .fi-input-wrp svg:not(.fi-icon-btn svg):not(button svg) {
        left: 0.75rem !important;
        right: auto !important;
        position: absolute !important;
        pointer-events: none !important;
        z-index: 1 !important;
    }
    
    /* Select Dropdown Arrow - More Specific */
    .fi-select .fi-input-wrp > svg,
    .fi-select .fi-input-wrp > .fi-icon,
    [x-data*="select"] .fi-input-wrp > svg,
    .fi-fo-field-wrp .fi-input-wrp > svg {
        left: 0.75rem !important;
        right: auto !important;
        position: absolute !important;
        pointer-events: none !important;
        z-index: 1 !important;
    }
    
    /* Ensure wrapper has relative positioning */
    .fi-select .fi-input-wrp,
    [x-data*="select"] .fi-input-wrp,
    .fi-fo-field-wrp .fi-input-wrp {
        position: relative !important;
    }
    
    /* Filament Select with Multiple Buttons (Clear + Dropdown) */
    .fi-input-wrp > button:first-of-type,
    .fi-input-wrp > .fi-icon-btn:first-of-type {
        right: 0.5rem !important;
        left: auto !important;
    }
    
    .fi-input-wrp > svg:last-of-type,
    .fi-input-wrp > .fi-icon:last-of-type:not(.fi-icon-btn) {
        left: 0.75rem !important;
        right: auto !important;
    }
</style>

