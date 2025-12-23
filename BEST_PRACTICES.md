# Best Practices: Frontend Development Guidelines

## Lessons Learned from Career Paths Section Fix

### 1. CSS Architecture & Layout

#### ✅ DO:
- **Use Flexbox/Grid for layouts** - Prefer semantic flexbox or CSS Grid over absolute positioning for component layouts
- **Keep CSS simple and maintainable** - Simple, readable CSS is easier to debug and maintain
- **Use Tailwind utility classes** - Leverage Tailwind's responsive utilities (`grid-cols-1 md:grid-cols-2 lg:grid-cols-4`)
- **Structure components with semantic HTML** - Use proper HTML structure before adding styling
- **Test responsive breakpoints** - Always verify mobile, tablet, and desktop layouts

#### ❌ DON'T:
- **Avoid complex absolute positioning** - Don't use absolute positioning for entire component layouts unless absolutely necessary
- **Don't use inline `!important` styles** - If you need `!important`, fix the root CSS conflict instead
- **Don't mix positioning strategies** - Avoid combining absolute, relative, and fixed positioning unnecessarily
- **Don't hardcode dimensions** - Use responsive units and Tailwind's spacing scale
- **Don't create CSS conflicts** - Be aware of global CSS files (like `desktop-figma.css`) that might override your styles

### 2. Alpine.js & Livewire Integration

#### ✅ DO:
- **Use Livewire's bundled Alpine** - Livewire v3 includes Alpine.js, no need to install separately
- **Register Alpine data via `alpine:init` event** - Use `document.addEventListener('alpine:init', ...)` to register custom data
- **Access Alpine via `window.Alpine`** - Always use the global `window.Alpine` object provided by Livewire
- **Test Alpine components after changes** - Verify modals, dropdowns, and interactive elements work

#### ❌ DON'T:
- **Don't install `alpinejs` separately** - This causes "multiple instances" warnings
- **Don't call `Alpine.start()` manually** - Livewire handles Alpine initialization
- **Don't import Alpine in your JS bundle** - Let Livewire manage Alpine lifecycle
- **Don't ignore Alpine warnings** - If you see "multiple instances", check for duplicate Alpine imports

### 3. Livewire Component Patterns

#### ✅ DO:
- **Use `wire:ignore` sparingly** - Only when you need to prevent Livewire from updating a specific DOM section
- **Keep components self-contained** - Each Blade component should be independent and reusable
- **Use Livewire's reactive properties** - Leverage `$wire` for reactive updates when needed
- **Structure components logically** - Separate concerns: header, content, footer sections

#### ❌ DON'T:
- **Don't wrap everything in `wire:ignore`** - Only use it when necessary (e.g., third-party widgets)
- **Don't mix Livewire and Alpine unnecessarily** - Use Livewire for server-side reactivity, Alpine for client-side interactivity
- **Don't create deep component nesting** - Keep component hierarchy shallow and readable

### 4. CSS Conflict Resolution

#### ✅ DO:
- **Use CSS specificity correctly** - Understand how CSS specificity works
- **Scope styles to components** - Use component-specific classes or Tailwind's component patterns
- **Check browser DevTools** - Inspect computed styles to find what's overriding your CSS
- **Use Tailwind's `@layer` directive** - Organize custom styles properly
- **Test in production builds** - Some CSS issues only appear in production

#### ❌ DON'T:
- **Don't use `!important` as a quick fix** - Fix the root cause instead
- **Don't fight with global CSS** - If global CSS conflicts, refactor or scope your styles
- **Don't inline critical styles** - Keep styles in CSS files for maintainability
- **Don't ignore CSS warnings** - Address CSS conflicts early

### 5. Component Development Workflow

#### ✅ DO:
- **Start with semantic HTML** - Write clean, accessible HTML first
- **Add Tailwind classes incrementally** - Build up styling layer by layer
- **Test at each breakpoint** - Verify mobile, tablet, desktop layouts
- **Use browser DevTools** - Inspect elements to debug layout issues
- **Keep components simple** - Prefer simple solutions over complex ones
- **Document complex logic** - Add comments for non-obvious CSS or JS

#### ❌ DON'T:
- **Don't copy-paste complex CSS** - Understand what you're using
- **Don't skip responsive testing** - Always test on multiple screen sizes
- **Don't use magic numbers** - Use Tailwind's design tokens instead
- **Don't ignore accessibility** - Ensure proper ARIA labels and semantic HTML

### 6. Debugging Strategies

#### ✅ DO:
- **Check browser console** - Look for JavaScript errors and warnings
- **Inspect computed styles** - Use DevTools to see what CSS is actually applied
- **Test with fresh browser cache** - Hard refresh (Ctrl+Shift+R) to clear cache
- **Verify production builds** - Test built assets, not just dev server
- **Check network tab** - Ensure all assets are loading correctly
- **Use accessibility tree** - Check if content is present but hidden

#### ❌ DON'T:
- **Don't assume cached content** - Always verify with hard refresh
- **Don't ignore console warnings** - Address warnings before they become errors
- **Don't debug in production** - Use development tools and source maps

### 7. File Organization

#### ✅ DO:
- **Keep components in logical directories** - `resources/views/components/home/` for home page components
- **Separate concerns** - Keep CSS, JS, and Blade templates organized
- **Use consistent naming** - Follow Laravel/Blade conventions
- **Group related components** - Keep related components together

#### ❌ DON'T:
- **Don't create deep nesting** - Avoid too many directory levels
- **Don't mix concerns** - Don't put CSS logic in Blade files unnecessarily
- **Don't duplicate code** - Extract common patterns into reusable components

### 8. Performance Considerations

#### ✅ DO:
- **Use Tailwind's purge/content config** - Ensure unused CSS is removed in production
- **Lazy load images** - Use `loading="lazy"` for below-the-fold images
- **Optimize asset builds** - Use Vite's production optimizations
- **Minimize JavaScript** - Only include what you need

#### ❌ DON'T:
- **Don't load unnecessary CSS** - Remove unused global CSS files if possible
- **Don't inline large assets** - Keep assets in separate files
- **Don't ignore build warnings** - Address Vite/build warnings

## Quick Reference Checklist

Before deploying a new component:

- [ ] HTML is semantic and accessible
- [ ] CSS uses Tailwind utilities (no inline `!important`)
- [ ] Responsive breakpoints tested (mobile, tablet, desktop)
- [ ] No console errors or warnings
- [ ] Alpine components work (if used)
- [ ] Livewire reactivity works (if used)
- [ ] Production build succeeds
- [ ] No duplicate dependencies in `package.json`
- [ ] Browser cache cleared and tested
- [ ] Accessibility tree shows all content

## Common Pitfalls to Avoid

1. **CSS Conflicts**: Always check for global CSS that might override your styles
2. **Alpine Duplication**: Never install `alpinejs` separately when using Livewire
3. **Absolute Positioning**: Use flexbox/grid instead for layouts
4. **Inline Styles**: Avoid `!important` inline styles - fix root cause
5. **Cache Issues**: Always hard refresh when debugging layout issues
6. **Missing Content**: Check accessibility tree if content exists but isn't visible

---

**Last Updated**: 2025-12-09  
**Based on**: Career Paths Section Fix

