PROJECT: KD Artisan Room — premium handmade gifts and crafts ecommerce (India)

STACK RULES (never change these):
- Backend: Laravel, Blade templates only — NO Livewire, NO Inertia
- Dynamic UI: Vanilla JS Fetch API for Ajax calls to Laravel JSON routes
- Animations: GSAP + ScrollTrigger
- Micro-interactions: Alpine.js (x-data, x-show, x-on only)
- Hero background: HTML5 Canvas particle animation (vanilla JS, no Three.js)
- Bundler: Vite
- Fonts: Playfair Display (headings), DM Sans or Space Grotesk (body)

BRAND: Forest Gold palette (never change these hex values)
- Light bg: #fafaf8
- Dark bg: #0d1410
- Deep forest: #1b4332
- Mid forest: #2d6a4f
- Gold accent: #d4a853
- Warm amber: #f4a261
- Light mode text: #1a1a1a
- Dark mode text: #f0ede4
- Muted text light: #6b7268 / Muted text dark: #8a9a8c

DARK/LIGHT MODE: must be fully supported, toggle stored in localStorage, 
default to system preference (prefers-color-scheme), applied via a 
data-theme attribute on <html>

LOADER: Hourglass loader, pure CSS animation, sand drains top chamber to 
bottom chamber in forest green and gold, shows on every page load for 2.5s

ARCHITECTURE RULES:
- Ajax endpoints return JSON, prefixed /api/ in web.php
- Controllers: app/Http/Controllers/Frontend/ for public, 
  app/Http/Controllers/Admin/ for admin
- Blade components: resources/views/components/
- Page views: resources/views/pages/
- CSS split: resources/css/components/*.css
- JS split: resources/js/modules/*.js

Always read CLAUDE.md before starting any task.
Always run php artisan route:list after touching routes.
Always run npm run build and fix errors before declaring a task done.