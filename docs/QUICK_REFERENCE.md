# 🎨 Fiche Design System - Quick Reference

## 🎯 Essential Classes

### Buttons

```html
<!-- Primary Action -->
<button class="fiche-btn fiche-btn-primary">
    <i class="fa fa-save"></i> Save
</button>

<!-- Secondary Action -->
<button class="fiche-btn fiche-btn-secondary">Cancel</button>

<!-- Danger Action -->
<button class="fiche-btn fiche-btn-danger">
    <i class="fa fa-trash"></i> Delete
</button>

<!-- Favorite Toggle -->
<button class="fiche-btn fiche-btn-favorite">
    <i class="fa fa-heart-o"></i>
</button>
<button class="fiche-btn fiche-btn-favorite-active">
    <i class="fa fa-heart"></i>
</button>

<!-- CTA Button -->
<a href="#" class="fiche-btn-neon">
    <i class="fa fa-plus-circle"></i> Create New
</a>
```

### Cards

```html
<!-- Basic Card -->
<div class="fiche-ultimate-card">
    <div class="fiche-card-header">
        <div class="fiche-card-icon">
            <i class="fa fa-file-text-o"></i>
        </div>
        <h3 class="fiche-card-title">Card Title</h3>
        <span class="fiche-card-badge">Badge</span>
    </div>
    <div class="fiche-card-body">
        <p class="fiche-card-excerpt">Content...</p>
    </div>
    <div class="fiche-card-actions">
        <button class="fiche-btn fiche-btn-primary">Action</button>
    </div>
</div>
```

### Grid Layout

```html
<div class="fiche-grid">
    <!-- Cards go here -->
</div>
```

### Editor Toolbar

```html
<div class="fiche-toolbar2">
    <button class="chip chip-warn">
        <span class="chip-ico">⚠️</span>
        <span class="chip-txt">Important</span>
        <span class="chip-kbd">Ctrl+1</span>
    </button>
</div>
```

### Preview Blocks

```html
<div class="fiche-preview2">
    <div class="fiche-block definition">⚠️ <strong>Definition</strong></div>
    <div class="fiche-block example">📘 <strong>Example</strong></div>
    <div class="fiche-block tip">💡 <strong>Tip</strong></div>
    <div class="fiche-block trap">🚫 <strong>Trap</strong></div>
    <div class="fiche-block question">❓ <strong>Question</strong></div>
</div>
```

---

## 🎨 Color Palette

### Brand Colors
```
Primary:   #667eea  ████
Secondary: #764ba2  ████
Accent:    #ec4899  ████
```

### Semantic Colors
```
Success:   #10b981  ████
Warning:   #f59e0b  ████
Danger:    #ef4444  ████
Info:      #3b82f6  ████
```

### Neutrals
```
Dark:      #1a202c  ████
Gray:      #4a5568  ████
Light:     #f7fafc  ████
```

---

## 📏 Spacing Scale

```
xs:   6px   ▪
sm:   8px   ▪▪
md:   12px  ▪▪▪
lg:   16px  ▪▪▪▪
xl:   24px  ▪▪▪▪▪▪
```

---

## 🎭 CSS Variables

### Most Used

```css
/* Colors */
var(--primary)
var(--secondary)
var(--text-primary)
var(--text-secondary)

/* Backgrounds */
var(--card-bg)
var(--card-border)

/* Spacing */
var(--radius)
var(--radius-lg)
var(--radius-full)

/* Effects */
var(--shadow-lg)
var(--shadow-glow)
var(--transition)
```

---

## 🎬 Common Animations

### Hover Effects

```css
/* Lift */
transform: translateY(-8px);

/* Glow */
box-shadow: var(--shadow-glow);

/* Scale */
transform: scale(1.05);

/* Rotate */
transform: rotate(5deg);
```

### Entrance Animations

```css
/* Fade In Up */
animation: fadeInUp 0.6s ease-out;

/* Slide In Left */
animation: slideInLeft 0.3s ease-out;

/* Pulse */
animation: pulse 2s infinite;
```

---

## 📱 Responsive Breakpoints

```css
/* Desktop */
@media (min-width: 992px) { }

/* Tablet */
@media (max-width: 991px) { }

/* Mobile */
@media (max-width: 768px) { }

/* Small Mobile */
@media (max-width: 480px) { }
```

---

## ♿ Accessibility

### Focus States

```css
.element:focus-visible {
    outline: 3px solid var(--primary);
    outline-offset: 2px;
}
```

### Reduced Motion

```css
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

---

## 🔧 Editor Syntax

### Block Syntax

```
== Section Title
!! Important Definition
>> Example
!!+ Tip/Astuce
!!- Trap/Piège
?? Question
```

### Inline Syntax

```
**bold**
_italic_
`code`
[[keyword]]
- list item
->  (arrow)
```

---

## 📦 File Structure

```
public/front/css/
├── fiche.css              # Legacy (buttons, checkboxes)
├── fiche-complete.css     # Main system (index, grid)
├── fiche-editor.css       # Editor interface
└── fiche-show.css         # Detail view

templates/fiche/
├── _layout.html.twig      # Base layout
├── index.html.twig        # Card grid
├── show.html.twig         # Detail view
└── edit.html.twig         # Editor
```

---

## 🚀 Quick Start

### 1. Include CSS

```twig
{% block css %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('front/css/fiche-complete.css') }}">
{% endblock %}
```

### 2. Use Components

```html
<div class="fiche-grid">
    <div class="fiche-ultimate-card">
        <!-- Card content -->
    </div>
</div>
```

### 3. Customize

```css
:root {
    --primary: #your-color;
}
```

---

## 🎯 Common Patterns

### Card with Actions

```html
<div class="fiche-ultimate-card">
    <div class="fiche-card-header">
        <div class="fiche-card-icon"><i class="fa fa-file"></i></div>
        <h3 class="fiche-card-title">Title</h3>
    </div>
    <div class="fiche-card-body">
        <p class="fiche-card-excerpt">Content</p>
    </div>
    <div class="fiche-card-actions">
        <button class="fiche-btn fiche-btn-primary">View</button>
        <button class="fiche-btn fiche-btn-secondary">Edit</button>
    </div>
</div>
```

### Filter Tabs

```html
<div class="fiche-filter-tabs">
    <a href="#" class="fiche-filter-tab active">
        <i class="fa fa-th"></i> All
    </a>
    <a href="#" class="fiche-filter-tab">
        <i class="fa fa-user"></i> Mine
    </a>
</div>
```

### Empty State

```html
<div class="fiche-empty">
    <div class="fiche-empty-icon">
        <i class="fa fa-book"></i>
    </div>
    <h2 class="fiche-empty-title">No Items</h2>
    <p class="fiche-empty-text">Get started by creating your first item.</p>
    <a href="#" class="fiche-btn-neon">Create Now</a>
</div>
```

---

## 🐛 Troubleshooting

### Cards not showing?
✅ Check: `fiche-complete.css` is loaded

### Buttons look wrong?
✅ Check: Using `fiche-btn` base class

### Grid not responsive?
✅ Check: Container has proper width

### Animations not working?
✅ Check: Browser supports CSS animations

### Colors look off?
✅ Check: CSS variables are defined

---

## 📚 Resources

- **Full Docs:** `/docs/FICHE_DESIGN_SYSTEM.md`
- **Changelog:** `/docs/DESIGN_UPGRADE_CHANGELOG.md`
- **Support:** design@baclab.tn

---

**Version:** 2.0.0  
**Last Updated:** February 12, 2026  
**Status:** Production Ready ✅
