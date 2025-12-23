# System Design Document - هوشکس (Hooshex)

## 📋 Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Design System](#design-system)
4. [Component Architecture](#component-architecture)
5. [CSS Organization](#css-organization)
6. [File Structure](#file-structure)
7. [Code Standards](#code-standards)
8. [RTL Support](#rtl-support)
9. [Responsive Strategy](#responsive-strategy)

---

## 1. Overview

This document defines the system design for the Hooshex website, ensuring consistent, maintainable, and scalable code across the entire application.

### Goals
- **Modularity**: Components should be reusable and self-contained
- **Consistency**: Unified design language across all pages
- **Maintainability**: Easy to update and extend
- **Performance**: Optimized CSS and minimal redundancy
- **Accessibility**: WCAG 2.1 AA compliance

---

## 2. Architecture

### 2.1 Frontend Stack
- **Framework**: Laravel 11/12 with Livewire v3
- **Styling**: Tailwind CSS v4 (with @theme)
- **JavaScript**: Alpine.js (bundled with Livewire)
- **Build Tool**: Vite
- **CSS Architecture**: Utility-first with component layer

### 2.2 Layer Structure

```
┌─────────────────────────────────────┐
│         Livewire Components         │  (Business Logic + State)
├─────────────────────────────────────┤
│      Blade Components Layer         │  (Reusable UI Components)
├─────────────────────────────────────┤
│      Design System / Tokens         │  (Colors, Typography, Spacing)
├─────────────────────────────────────┤
│      Base Styles (Tailwind)         │  (Utilities, Reset)
└─────────────────────────────────────┘
```

---

## 3. Design System

### 3.1 Color System

#### Primary Colors
```javascript
primary: {
  50: 'rgba(119, 95, 238, 0.1)',   // Light tint for backgrounds
  400: '#442CBB',                   // Medium shade
  500: '#775FEE',                   // Base primary
  600: '#5537EA',                   // Hover/active states
  800: '#22165E',                   // Dark shade for text
}
```

#### Accent Colors
```javascript
accent: {
  400: 'rgba(235, 85, 200, 0.36)',  // Light accent
  500: '#EB55C8',                    // Base accent (pink)
}
```

#### Neutral Colors
```javascript
surface: '#FCF1FB',                 // Main background
surface-light: '#F5F5F5',           // Light surfaces
text: {
  primary: '#22165E',               // Main text
  secondary: '#2D2D2D',             // Secondary text
  muted: '#AAAAAA',                 // Muted text
  light: '#FCF1FB',                 // Light text on dark
}
```

#### Semantic Colors
```javascript
success: '#10B981',
warning: '#F59E0B',
error: '#EF4444',
info: '#3B82F6',
```

### 3.2 Typography System

#### Font Family
- **Primary**: 'Vazirmatn', sans-serif
- **Fallback**: system-ui, sans-serif

#### Font Features (Persian Support)
```css
font-feature-settings: 'ss01' on, 'ss02' on, 'ss03' on, 'ss04' on;
font-variation-settings: 'DOTS' 7;
```

#### Type Scale
```javascript
fontSize: {
  xs: ['12px', { lineHeight: '18px' }],    // 0.75rem / 1.125rem
  sm: ['14px', { lineHeight: '21px' }],    // 0.875rem / 1.3125rem
  base: ['16px', { lineHeight: '24px' }],  // 1rem / 1.5rem
  lg: ['18px', { lineHeight: '27px' }],    // 1.125rem / 1.6875rem
  xl: ['20px', { lineHeight: '30px' }],    // 1.25rem / 1.875rem
  '2xl': ['24px', { lineHeight: '36px' }], // 1.5rem / 2.25rem
  '3xl': ['32px', { lineHeight: '48px' }], // 2rem / 3rem
  '4xl': ['36px', { lineHeight: '54px' }], // 2.25rem / 3.375rem
  '5xl': ['56px', { lineHeight: '84px' }], // 3.5rem / 5.25rem
}

fontWeight: {
  normal: 400,
  medium: 500,
  semibold: 600,
  bold: 700,
  black: 900,
}
```

### 3.3 Spacing System

Based on 4px base unit:
```javascript
spacing: {
  0: '0',
  1: '4px',    // 0.25rem
  2: '8px',    // 0.5rem
  3: '12px',   // 0.75rem
  4: '16px',   // 1rem
  5: '20px',   // 1.25rem
  6: '24px',   // 1.5rem
  8: '32px',   // 2rem
  10: '40px',  // 2.5rem
  12: '48px',  // 3rem
  16: '64px',  // 4rem
  20: '80px',  // 5rem
  24: '96px',  // 6rem
}
```

### 3.4 Border Radius

```javascript
borderRadius: {
  none: '0',
  sm: '4px',      // 0.25rem
  DEFAULT: '8px', // 0.5rem
  md: '12px',     // 0.75rem
  lg: '16px',     // 1rem
  xl: '24px',     // 1.5rem
  '2xl': '32px',  // 2rem
  pill: '9999px', // Full rounded
}
```

### 3.5 Breakpoints

```javascript
screens: {
  sm: '430px',   // Mobile
  md: '768px',   // Tablet
  lg: '1024px',  // Desktop
  xl: '1280px',  // Large Desktop
  '2xl': '1440px', // Extra Large Desktop
}
```

---

## 4. Component Architecture

### 4.1 Component Hierarchy

```
resources/views/components/
├── layouts/
│   └── app.blade.php              # Main layout
├── home/
│   ├── hero.blade.php             # Hero section
│   ├── features.blade.php         # Features grid
│   ├── career-paths.blade.php     # Career paths section
│   ├── super-app.blade.php        # Super app section
│   ├── popular-courses.blade.php  # Popular courses section
│   ├── ai-bot.blade.php           # AI bot generator section
│   ├── instagram.blade.php        # Instagram CTA section
│   ├── testimonials.blade.php     # Testimonials section
│   ├── blog.blade.php             # Blog section
│   ├── banner.blade.php           # Banner section
│   └── [card components]
├── ui/
│   ├── button.blade.php           # Button component
│   ├── card.blade.php             # Card component
│   ├── input.blade.php            # Input component
│   ├── badge.blade.php            # Badge component
│   ├── section-header.blade.php   # Section header
│   └── swiper-slider.blade.php    # Swiper wrapper
└── [other domain components]
```

### 4.2 Component Structure Pattern

Each component should follow this structure:

```blade
{{-- Component: component-name.blade.php --}}
@props([
    'variant' => 'default',
    'size' => 'md',
    // ... other props
])

<div {{ $attributes->merge([
    'class' => 'component-base-class',
]) }}>
    {{-- Component content --}}
</div>
```

### 4.3 Component Categories

#### 4.3.1 Layout Components
- `layouts/app.blade.php` - Main application layout
- `layouts/section.blade.php` - Section wrapper with consistent spacing

#### 4.3.2 UI Components (Atomic Design)
- **Atoms**: Button, Input, Badge, Icon
- **Molecules**: SearchForm, Card, TestimonialCard
- **Organisms**: HeroSection, FeaturesGrid, CourseSlider

#### 4.3.3 Page Components
- `home/*` - Home page specific components
- `courses/*` - Course page components
- `blog/*` - Blog page components

---

## 5. CSS Organization

### 5.1 CSS Layer Structure

```css
/* resources/css/app.css */

/* 1. Base Layer */
@import 'tailwindcss/base';
@import './base/fonts.css';
@import './base/reset.css';
@import './base/typography.css';

/* 2. Components Layer */
@import 'tailwindcss/components';
@import './components/buttons.css';
@import './components/cards.css';
@import './components/forms.css';
@import './components/liquid-glass.css';
@import './components/swiper.css';

/* 3. Utilities Layer */
@import 'tailwindcss/utilities';
@import './utilities/rtl.css';
@import './utilities/animations.css';
```

### 5.2 File Organization

```
resources/css/
├── app.css                    # Main entry point
├── base/
│   ├── fonts.css             # Font definitions
│   ├── reset.css             # CSS reset
│   └── typography.css        # Typography base styles
├── components/
│   ├── buttons.css           # Button variants
│   ├── cards.css             # Card styles
│   ├── forms.css             # Form elements
│   ├── liquid-glass.css      # Glass morphism effect
│   └── swiper.css            # Swiper customization
└── utilities/
    ├── rtl.css               # RTL utilities
    └── animations.css        # Animation utilities
```

### 5.3 CSS Rules

1. **No Inline Styles**: Use Tailwind classes or CSS components
2. **No !important**: Use specificity or component classes
3. **Responsive First**: Mobile-first approach
4. **Component Scoping**: Use component classes to avoid conflicts

---

## 6. File Structure

### 6.1 Recommended Structure

```
resources/views/
├── components/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── home/
│   │   ├── hero.blade.php
│   │   ├── features.blade.php
│   │   ├── career-paths.blade.php
│   │   ├── super-app.blade.php
│   │   ├── popular-courses.blade.php
│   │   ├── ai-bot.blade.php
│   │   ├── instagram.blade.php
│   │   ├── testimonials.blade.php
│   │   ├── blog.blade.php
│   │   ├── banner.blade.php
│   │   ├── course-card.blade.php
│   │   ├── testimonial-card.blade.php
│   │   └── blog-card.blade.php
│   └── ui/
│       ├── button.blade.php
│       ├── card.blade.php
│       ├── input.blade.php
│       ├── badge.blade.php
│       ├── section-header.blade.php
│       └── swiper-slider.blade.php
├── livewire/
│   └── home.blade.php          # Main page composition
└── [other views]
```

---

## 7. Code Standards

### 7.1 Blade Components

#### Props Definition
```blade
@props([
    'title' => '',
    'variant' => 'default',
    'size' => 'md',
])
```

#### Attribute Merging
```blade
<div {{ $attributes->merge([
    'class' => 'base-classes',
    'dir' => 'rtl',
]) }}>
```

#### Conditional Classes
```blade
<div class="{{ 
    'base-class ' . 
    ($variant === 'primary' ? 'variant-primary' : 'variant-default') 
}}">
```

### 7.2 Naming Conventions

- **Components**: kebab-case (e.g., `course-card.blade.php`)
- **Classes**: Tailwind utilities or kebab-case (e.g., `card-container`)
- **Variables**: camelCase in PHP, kebab-case in CSS

### 7.3 Code Organization

1. **Props** at the top
2. **Logic** (PHP) before HTML
3. **HTML structure** follows semantic order
4. **Classes** organized logically (layout → spacing → typography → colors)

---

## 8. RTL Support

### 8.1 RTL Rules

1. **Always use `dir="rtl"`** on root elements
2. **Justify alignment**: `justify-start` = right, `justify-end` = left
3. **Text alignment**: Use `text-right` for Persian text
4. **Icons**: Place icons FIRST in HTML for right-side positioning
5. **Spacing**: Use logical properties when needed

### 8.2 RTL Utilities

```css
/* utilities/rtl.css */
[dir="rtl"] .rtl-flip {
    transform: scaleX(-1);
}
```

---

## 9. Responsive Strategy

### 9.1 Mobile-First Approach

```blade
<!-- Mobile default -->
<div class="text-base">

<!-- Tablet and up -->
<div class="text-base md:text-lg">

<!-- Desktop and up -->
<div class="text-base md:text-lg lg:text-xl">
```

### 9.2 Breakpoint Usage

- **Mobile (< 430px)**: Base styles, no prefix
- **Tablet (≥ 768px)**: Use `md:` prefix
- **Desktop (≥ 1024px)**: Use `lg:` prefix
- **Large Desktop (≥ 1280px)**: Use `xl:` prefix

### 9.3 Container Sizes

```javascript
container: {
  sm: '430px',
  md: '768px',
  lg: '1024px',
  xl: '1280px',
  '2xl': '1440px',
}
```

---

## 10. Performance Considerations

### 10.1 CSS Optimization

1. **Purge unused CSS** via Tailwind purge
2. **Use CSS variables** for theming
3. **Minimize custom CSS** in favor of Tailwind utilities
4. **Critical CSS** for above-the-fold content

### 10.2 Image Optimization

1. **Lazy loading** for below-the-fold images
2. **Responsive images** with srcset
3. **WebP format** with fallbacks
4. **Optimized file sizes**

### 10.3 JavaScript

1. **No jQuery** or heavy libraries
2. **Alpine.js** for interactivity (bundled with Livewire)
3. **Defer non-critical scripts**

---

## 11. Accessibility Guidelines

### 11.1 Semantic HTML

- Use proper heading hierarchy (h1 → h2 → h3)
- Use semantic elements (nav, section, article, etc.)
- Label form inputs properly

### 11.2 ARIA Attributes

```blade
<button aria-label="Close menu">
<nav aria-label="Main navigation">
<div role="alert" aria-live="polite">
```

### 11.3 Keyboard Navigation

- All interactive elements should be keyboard accessible
- Focus indicators should be visible
- Logical tab order

---

## 12. Testing Strategy

### 12.1 Visual Testing

- Test on all breakpoints
- Test in RTL mode
- Test with different content lengths

### 12.2 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

---

## 13. Migration Strategy

### Phase 1: Setup
- ✅ Create design system tokens
- ✅ Organize CSS structure
- ✅ Create base components

### Phase 2: Component Extraction
- Extract sections from home page
- Create reusable UI components
- Refactor inline styles to classes

### Phase 3: Consistency
- Apply design system across all pages
- Remove redundant CSS files
- Standardize component patterns

---

## 14. Maintenance

### 14.1 Documentation

- Keep this document updated
- Document component props and usage
- Maintain changelog

### 14.2 Code Review

- Check for design system compliance
- Verify RTL support
- Test responsive behavior
- Check accessibility

---

## 15. Resources

- [Tailwind CSS Documentation](https://tailwindcss.com)
- [Livewire Documentation](https://livewire.laravel.com)
- [Alpine.js Documentation](https://alpinejs.dev)
- [WCAG Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

