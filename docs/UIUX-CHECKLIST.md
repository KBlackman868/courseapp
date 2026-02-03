# UI/UX Checklist

## Overview

This checklist covers all aspects of UI/UX that should be implemented or verified across the application.

---

## 1. Navigation

### Desktop Navigation
- [ ] Logo links to home
- [ ] Navigation items highlight active route
- [ ] Dropdown menus open on click
- [ ] Dropdown menus close when clicking outside
- [ ] Dropdown menus close when pressing Escape
- [ ] Dropdown items are keyboard navigable (Tab, Arrow keys)
- [ ] Focus states are visible
- [ ] Hover states provide feedback

### Mobile Navigation
- [ ] Hamburger menu button is visible on mobile
- [ ] Menu opens when clicking hamburger
- [ ] X button closes the menu
- [ ] Clicking a link closes the menu
- [ ] Clicking outside/overlay closes the menu
- [ ] Menu has slide animation
- [ ] User info is displayed in mobile menu
- [ ] Sign out option is accessible

### Breadcrumbs
- [ ] Show current location in hierarchy
- [ ] Links work correctly
- [ ] Current page is not a link
- [ ] Home icon at start

---

## 2. Loading States

### Page Loading
- [ ] Show skeleton loaders for content
- [ ] Show loading spinner for async operations
- [ ] Disable buttons during form submission
- [ ] Show progress indicators for long operations

### Component Loading
- [ ] Tables show loading state
- [ ] Cards show placeholder content
- [ ] Images have loading placeholders
- [ ] Forms disable during submission

### Implementation Examples:
```jsx
// Button loading state
<Button loading={isSubmitting}>
  {isSubmitting ? 'Saving...' : 'Save'}
</Button>

// Table loading state
<DataTable loading={isLoading} data={data} />
```

---

## 3. Empty States

### Tables
- [ ] Show message when no data
- [ ] Show icon/illustration
- [ ] Provide action to add first item
- [ ] Different messages for "no results" vs "no data"

### Lists
- [ ] Empty state with call to action
- [ ] Helpful guidance text

### Search Results
- [ ] "No results found" message
- [ ] Suggestions to modify search
- [ ] Clear search button

### Implementation Example:
```jsx
<DataTable
  data={data}
  emptyMessage="No courses found"
  emptyIcon={AcademicCapIcon}
  emptyAction={
    <Button onClick={handleCreate}>Create your first course</Button>
  }
/>
```

---

## 4. Error States

### Form Validation
- [ ] Inline error messages below fields
- [ ] Error styling on invalid fields (red border)
- [ ] Focus first error field on submit
- [ ] Clear error when user starts typing
- [ ] Show success state after correction

### API Errors
- [ ] Toast/notification for API errors
- [ ] Retry option where applicable
- [ ] Friendly error messages (not technical)
- [ ] Fallback UI for critical errors

### 404/Error Pages
- [ ] Custom 404 page
- [ ] Custom 500 page
- [ ] Link back to home
- [ ] Search/navigation options

### Implementation Example:
```jsx
// Form field with error
<div className="space-y-1">
  <label className="block text-sm font-medium text-gray-700">
    Email
  </label>
  <input
    type="email"
    className={`block w-full rounded-md ${
      errors.email
        ? 'border-red-300 text-red-900 focus:ring-red-500'
        : 'border-gray-300 focus:ring-indigo-500'
    }`}
  />
  {errors.email && (
    <p className="text-sm text-red-600">{errors.email}</p>
  )}
</div>
```

---

## 5. Table Patterns

### Features
- [ ] Sortable columns (click header)
- [ ] Search/filter functionality
- [ ] Pagination
- [ ] Row selection (if needed)
- [ ] Row actions (edit, delete, view)
- [ ] Bulk actions (if needed)
- [ ] Column visibility toggle (optional)
- [ ] Export functionality (optional)

### Responsive Behavior
- [ ] Horizontal scroll on mobile
- [ ] Or card layout on mobile
- [ ] Sticky header on scroll
- [ ] Show most important columns first

### Visual Design
- [ ] Alternating row colors (optional)
- [ ] Hover state on rows
- [ ] Clear header styling
- [ ] Proper alignment (numbers right, text left)
- [ ] Consistent column widths

---

## 6. Mobile Responsiveness

### Breakpoints
- [ ] Mobile: < 640px (sm)
- [ ] Tablet: 640px - 1024px (md/lg)
- [ ] Desktop: > 1024px (lg/xl)

### Touch Targets
- [ ] Minimum 44x44px touch targets
- [ ] Adequate spacing between interactive elements
- [ ] No hover-only interactions on mobile

### Layout Adaptations
- [ ] Stack horizontal layouts vertically
- [ ] Collapse sidebar to drawer
- [ ] Full-width forms on mobile
- [ ] Simplified navigation
- [ ] Hidden secondary actions in menus

### Testing
- [ ] Test on actual mobile devices
- [ ] Test in Chrome DevTools mobile mode
- [ ] Test landscape orientation
- [ ] Test with different text sizes

---

## 7. Dark Mode (Optional)

### Implementation
- [ ] System preference detection
- [ ] Manual toggle option
- [ ] Persist user preference
- [ ] Smooth transition

### Color Tokens
```css
/* Light mode */
--bg-primary: white;
--bg-secondary: #f9fafb;
--text-primary: #111827;
--text-secondary: #6b7280;

/* Dark mode */
--bg-primary: #1f2937;
--bg-secondary: #111827;
--text-primary: #f9fafb;
--text-secondary: #9ca3af;
```

### Testing
- [ ] All text readable
- [ ] Sufficient contrast ratios
- [ ] Images/icons visible
- [ ] Form elements styled
- [ ] No hardcoded colors

---

## 8. Accessibility (a11y)

### Semantic HTML
- [ ] Use correct heading hierarchy (h1, h2, h3...)
- [ ] Use `<nav>`, `<main>`, `<aside>`, `<footer>`
- [ ] Use `<button>` for actions, `<a>` for navigation
- [ ] Use `<ul>`/`<ol>` for lists

### ARIA Attributes
- [ ] `aria-label` on icon-only buttons
- [ ] `aria-expanded` on toggles/dropdowns
- [ ] `aria-current="page"` on active nav items
- [ ] `aria-describedby` for form errors
- [ ] `role="alert"` for dynamic error messages

### Keyboard Navigation
- [ ] All interactive elements focusable
- [ ] Logical tab order
- [ ] Escape closes modals/dropdowns
- [ ] Enter/Space activates buttons
- [ ] Arrow keys for menu navigation

### Focus Management
- [ ] Visible focus indicators
- [ ] Focus trapped in modals
- [ ] Focus returned after modal close
- [ ] Skip links for main content

### Screen Readers
- [ ] Alt text on images
- [ ] Hidden decorative elements
- [ ] Announce dynamic content changes
- [ ] Labels for form inputs

---

## 9. Consistent Design System

### Spacing Scale
```
4px  (1)  - tight
8px  (2)  - compact
12px (3)  - default small
16px (4)  - default
24px (6)  - relaxed
32px (8)  - spacious
48px (12) - section
64px (16) - large section
```

### Typography Scale
```
text-xs   - 12px - Meta text, badges
text-sm   - 14px - Body text, labels
text-base - 16px - Default body
text-lg   - 18px - Subheadings
text-xl   - 20px - Card titles
text-2xl  - 24px - Page headings
text-3xl  - 30px - Main headings
```

### Border Radius
```
rounded-sm  - 2px  - Subtle
rounded     - 4px  - Default
rounded-md  - 6px  - Cards, inputs
rounded-lg  - 8px  - Modals, panels
rounded-xl  - 12px - Large cards
rounded-full       - Avatars, badges
```

### Shadows
```
shadow-sm   - Subtle elevation
shadow      - Cards
shadow-md   - Dropdowns
shadow-lg   - Modals
shadow-xl   - Popovers
```

### Color Palette
```
Primary:   indigo-600 (buttons, links, focus)
Secondary: gray-600 (secondary text)
Success:   green-600 (success states)
Warning:   yellow-500 (warnings)
Error:     red-600 (errors)
Info:      blue-600 (information)
```

---

## 10. Performance Considerations

### Images
- [ ] Use appropriate formats (WebP, AVIF)
- [ ] Lazy load below-fold images
- [ ] Provide width/height to prevent layout shift
- [ ] Use responsive images (srcset)

### JavaScript
- [ ] Code split large components
- [ ] Lazy load non-critical features
- [ ] Debounce search inputs
- [ ] Virtualize long lists

### CSS
- [ ] Purge unused CSS in production
- [ ] Avoid complex selectors
- [ ] Use CSS containment where applicable

---

## 11. Specific Component Checklist

### Buttons
- [ ] Primary variant (main actions)
- [ ] Secondary variant (secondary actions)
- [ ] Danger variant (destructive actions)
- [ ] Ghost variant (subtle actions)
- [ ] Loading state
- [ ] Disabled state
- [ ] With icon variants
- [ ] Size variants (sm, md, lg)

### Forms
- [ ] Text input
- [ ] Email input with validation
- [ ] Password input with show/hide
- [ ] Textarea with character count
- [ ] Select/dropdown
- [ ] Checkbox
- [ ] Radio group
- [ ] Toggle/switch
- [ ] Date picker
- [ ] File upload
- [ ] Form validation
- [ ] Required field indicators

### Modals/Dialogs
- [ ] Centered on screen
- [ ] Backdrop overlay
- [ ] Close on backdrop click
- [ ] Close on Escape
- [ ] Close button in header
- [ ] Focus trap
- [ ] Animation in/out
- [ ] Multiple sizes

### Notifications/Toasts
- [ ] Success variant
- [ ] Error variant
- [ ] Warning variant
- [ ] Info variant
- [ ] Auto-dismiss with progress
- [ ] Manual dismiss
- [ ] Stacking multiple
- [ ] Action buttons

### Dropdowns
- [ ] Open on click
- [ ] Close on outside click
- [ ] Close on Escape
- [ ] Keyboard navigation
- [ ] Proper z-index
- [ ] Animations

---

## 12. Testing Checklist

### Manual Testing
- [ ] Test all user flows
- [ ] Test on Chrome, Firefox, Safari, Edge
- [ ] Test on mobile devices
- [ ] Test with keyboard only
- [ ] Test with screen reader
- [ ] Test with zoom (up to 200%)

### Automated Testing
- [ ] Unit tests for components
- [ ] Integration tests for flows
- [ ] Visual regression tests
- [ ] Accessibility audits (axe, Lighthouse)

---

## Quick Reference: Common Patterns

### Page Layout
```jsx
<Layout>
  <PageHeader
    title="Page Title"
    description="Page description"
    actions={<Button>Primary Action</Button>}
  />
  <div className="mt-6">
    {/* Page content */}
  </div>
</Layout>
```

### Card Grid
```jsx
<div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
  <Card>...</Card>
  <Card>...</Card>
  <Card>...</Card>
</div>
```

### Stats Row
```jsx
<div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
  <StatCard title="Users" value="1,234" icon={UsersIcon} />
  <StatCard title="Courses" value="45" icon={AcademicCapIcon} />
</div>
```

### Two Column Layout
```jsx
<div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
  <Card>Left content</Card>
  <Card>Right content</Card>
</div>
```

---

## Sign-off

- [ ] All items checked
- [ ] Reviewed by designer
- [ ] Reviewed by developer
- [ ] Tested by QA
- [ ] Approved for release
