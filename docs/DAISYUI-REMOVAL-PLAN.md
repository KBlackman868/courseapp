# DaisyUI Removal Plan

## Overview

This document outlines the step-by-step process to remove DaisyUI from the application and replace it with pure Tailwind CSS + Headless UI.

---

## Phase 1: Preparation

### 1.1 Backup Current State
```bash
git checkout -b ui-migration-backup
git add -A && git commit -m "Backup before DaisyUI removal"
```

### 1.2 Install Required Packages
```bash
npm install @headlessui/react @heroicons/react
```

### 1.3 Update tailwind.config.js

**Before:**
```js
module.exports = {
  plugins: [
    require('daisyui'),
  ],
  daisyui: {
    themes: ['light', 'dark'],
  },
}
```

**After:**
```js
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.jsx',
  ],
  theme: {
    extend: {
      // Add your custom design tokens here
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
```

---

## Phase 2: DaisyUI Class Patterns to Search & Replace

### 2.1 Layout Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `drawer` | Custom implementation with Headless UI Dialog |
| `drawer-toggle` | Remove (use state) |
| `drawer-content` | `flex flex-col` |
| `drawer-side` | Headless UI Dialog/Slide-over |
| `drawer-overlay` | `fixed inset-0 bg-gray-900/80` |
| `navbar` | `flex items-center justify-between h-16 px-4 bg-white border-b` |

### 2.2 Button Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `btn` | `inline-flex items-center px-4 py-2 rounded-md font-medium shadow-sm` |
| `btn-primary` | `bg-indigo-600 text-white hover:bg-indigo-700` |
| `btn-secondary` | `bg-white text-gray-700 border border-gray-300 hover:bg-gray-50` |
| `btn-ghost` | `bg-transparent hover:bg-gray-100` |
| `btn-error` | `bg-red-600 text-white hover:bg-red-700` |
| `btn-success` | `bg-green-600 text-white hover:bg-green-700` |
| `btn-warning` | `bg-yellow-500 text-white hover:bg-yellow-600` |
| `btn-sm` | `px-3 py-1.5 text-sm` |
| `btn-lg` | `px-6 py-3 text-lg` |
| `btn-circle` | `rounded-full p-2` |
| `btn-square` | `p-2 aspect-square` |

### 2.3 Badge Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `badge` | `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium` |
| `badge-primary` | `bg-indigo-100 text-indigo-800` |
| `badge-secondary` | `bg-gray-100 text-gray-800` |
| `badge-success` | `bg-green-100 text-green-800` |
| `badge-warning` | `bg-yellow-100 text-yellow-800` |
| `badge-error` | `bg-red-100 text-red-800` |
| `badge-sm` | `px-2 py-0.5 text-xs` |

### 2.4 Card Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `card` | `bg-white rounded-lg shadow` |
| `card-body` | `p-6` |
| `card-title` | `text-lg font-semibold text-gray-900` |
| `card-actions` | `flex gap-2 mt-4` |
| `card-compact` | `p-4` |

### 2.5 Form Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `input` | `block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600` |
| `input-bordered` | (included above) |
| `input-primary` | `focus:ring-indigo-600` |
| `input-error` | `ring-red-500 focus:ring-red-500` |
| `select` | Same as input |
| `textarea` | Same as input |
| `checkbox` | `h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600` |
| `toggle` | Custom switch component |
| `form-control` | `space-y-1` |
| `label` | `block text-sm font-medium text-gray-700` |

### 2.6 Menu/Dropdown Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `dropdown` | Headless UI Menu component |
| `dropdown-content` | Headless UI MenuItems |
| `menu` | `space-y-1` |
| `menu-title` | `px-3 py-2 text-xs font-semibold text-gray-500 uppercase` |
| `menu-item` | Headless UI MenuItem |

### 2.7 Modal Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `modal` | Headless UI Dialog |
| `modal-box` | `bg-white rounded-lg shadow-xl p-6 max-w-md mx-auto` |
| `modal-action` | `flex gap-2 justify-end mt-6` |
| `modal-backdrop` | `fixed inset-0 bg-black/50` |

### 2.8 Table Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `table` | `min-w-full divide-y divide-gray-200` |
| `table-zebra` | Use `even:bg-gray-50` on rows |
| `table-compact` | Reduce padding |

### 2.9 Alert Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `alert` | `rounded-md p-4` |
| `alert-info` | `bg-blue-50 text-blue-800 border border-blue-200` |
| `alert-success` | `bg-green-50 text-green-800 border border-green-200` |
| `alert-warning` | `bg-yellow-50 text-yellow-800 border border-yellow-200` |
| `alert-error` | `bg-red-50 text-red-800 border border-red-200` |

### 2.10 Avatar Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `avatar` | `relative` |
| `avatar-placeholder` | `flex items-center justify-center bg-gray-200 text-gray-600` |
| `rounded-full` | `rounded-full` (same) |
| `ring` | `ring-2 ring-white` |

### 2.11 Theme Classes

| DaisyUI Class | Tailwind Replacement |
|---------------|---------------------|
| `bg-base-100` | `bg-white` |
| `bg-base-200` | `bg-gray-50` |
| `bg-base-300` | `bg-gray-100` |
| `text-base-content` | `text-gray-900` |
| `text-primary` | `text-indigo-600` |
| `text-secondary` | `text-gray-600` |
| `text-error` | `text-red-600` |

---

## Phase 3: Search Commands

Use these grep/search patterns to find DaisyUI usage:

```bash
# Find all DaisyUI class patterns in Blade files
grep -rn "btn-\|badge-\|card-\|alert-\|modal-\|dropdown-\|drawer-\|navbar\|menu-\|table-\|avatar\|base-100\|base-200\|base-content" resources/views/

# Find all DaisyUI class patterns in JS/JSX files
grep -rn "btn-\|badge-\|card-\|alert-\|modal-\|dropdown-\|drawer-\|navbar\|menu-\|table-\|avatar\|base-100\|base-200\|base-content" resources/js/

# Count occurrences
grep -roh "btn-[a-z]*" resources/ | sort | uniq -c | sort -rn
```

---

## Phase 4: File-by-File Migration

### Priority Order:

1. **Layouts** (highest impact)
   - `resources/views/components/layouts.blade.php`
   - `resources/js/Layouts/AuthenticatedLayout.jsx`
   - `resources/js/Layouts/GuestLayout.jsx`

2. **Components**
   - All files in `resources/js/Components/`
   - All files in `resources/views/components/`

3. **Pages**
   - Dashboard pages
   - Admin pages
   - Auth pages
   - Profile pages

---

## Phase 5: Component Folder Structure

```
resources/js/Components/
├── UI/
│   ├── index.js           # Re-exports all UI components
│   ├── Button.jsx         # Button, LinkButton, IconButton
│   ├── Badge.jsx          # Badge, StatusBadge
│   ├── Card.jsx           # Card, CardHeader, CardBody, CardFooter
│   ├── DataTable.jsx      # DataTable with search, sort, pagination
│   ├── PageHeader.jsx     # Page header with breadcrumbs
│   ├── StatCard.jsx       # Stat/metric cards
│   ├── Modal.jsx          # Modal dialog (Headless UI)
│   ├── Dropdown.jsx       # Dropdown menu (Headless UI)
│   ├── Alert.jsx          # Alert/notification banners
│   ├── Avatar.jsx         # Avatar with fallback
│   ├── Input.jsx          # Form input
│   ├── Select.jsx         # Form select
│   └── Toggle.jsx         # Toggle switch
├── Forms/
│   ├── FormGroup.jsx
│   ├── FormLabel.jsx
│   └── FormError.jsx
└── Navigation/
    ├── NavLink.jsx
    ├── MobileNav.jsx
    └── Sidebar.jsx
```

---

## Phase 6: Remove DaisyUI

### 6.1 Uninstall Package
```bash
npm uninstall daisyui
```

### 6.2 Update tailwind.config.js
Remove DaisyUI from plugins array.

### 6.3 Rebuild Assets
```bash
npm run build
```

### 6.4 Test Thoroughly
- Test all pages visually
- Test all interactive components
- Test responsive layouts
- Test dark mode (if applicable)

---

## Phase 7: Post-Migration Cleanup

1. Remove any unused CSS
2. Remove data-theme attributes
3. Update any documentation
4. Run final visual QA

---

## Estimated Timeline

| Phase | Estimated Time |
|-------|---------------|
| Phase 1: Preparation | 1 hour |
| Phase 2-3: Analysis | 2 hours |
| Phase 4: Layouts | 4-6 hours |
| Phase 4: Components | 4-6 hours |
| Phase 4: Pages | 6-8 hours |
| Phase 5-6: Cleanup | 2 hours |
| Phase 7: Testing | 2-4 hours |
| **Total** | **21-29 hours** |

---

## Notes

- Keep the old code commented during migration for reference
- Test each component after migration before moving to the next
- Use browser dev tools to compare before/after styling
- Document any design decisions made during migration
