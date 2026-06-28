import './modules/theme';
import './bootstrap';
import '@phosphor-icons/web/regular';

import Alpine from 'alpinejs';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { initHeroCanvas } from './modules/hero-canvas';
import { initShopFilters } from './modules/shop-filters';
import { initCart } from './modules/cart';
import { initCartPage } from './modules/cart-page';
import { initCheckout } from './modules/checkout';




// Register GSAP ScrollTrigger plugin
gsap.registerPlugin(ScrollTrigger);

// Expose on window for global access
window.Alpine = Alpine;
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;

// Start Alpine
Alpine.start();

// Initialize HTML5 Hero Canvas animation on screens wider than 768px
document.addEventListener('DOMContentLoaded', () => {
    if (window.innerWidth > 768) {
        const destroyCanvas = initHeroCanvas('hero-canvas');

        // Clean up on page unload
        if (destroyCanvas) {
            window.addEventListener('beforeunload', destroyCanvas, { once: true });
        }
    }

    // GSAP Homepage Hero Entrance Animation
    const heroHeading = document.querySelector('.hero-heading');
    if (heroHeading) {
        // Start timeline after loader fades out (loader 2.5s + 0.3s delay)
        const tl = gsap.timeline({ delay: 2.8 });

        // 1. Eyebrow fades & slides in
        tl.to('.hero-eyebrow', {
            opacity: 1,
            y: 0,
            duration: 0.8,
            ease: 'power3.out'
        });

        // 2. Heading lines slide up staggered
        tl.to('.hero-heading span span', {
            y: 0,
            opacity: 1,
            duration: 1.0,
            stagger: 0.15,
            ease: 'power4.out'
        }, '-=0.5');

        // 3. Subtext and CTA fade & slide up
        tl.to(['.hero-subtext', '.hero-cta'], {
            opacity: 1,
            y: 0,
            duration: 0.8,
            stagger: 0.15,
            ease: 'power3.out'
        }, '-=0.6');
    }

    // GSAP ScrollTrigger for Featured Product Cards
    const featuredCards = document.querySelectorAll('.featured-card-wrapper');
    if (featuredCards.length > 0) {
        gsap.fromTo(featuredCards,
            { opacity: 0, y: 40 },
            {
                opacity: 1,
                y: 0,
                duration: 1.0,
                stagger: 0.15,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: '#featured-section',
                    start: 'top 80%',
                    toggleActions: 'play none none none'
                }
            }
        );
    }

    // Initialize global cart count & event triggers
    initCart();

    // Initialize shop filters if on shop page
    if (document.getElementById('product-grid')) {
        initShopFilters();
    }

    // Initialize cart page controls if on cart page
    if (document.getElementById('cart-page-wrapper')) {
        initCartPage();
    }

    // Initialize checkout page controls if on checkout page
    if (document.getElementById('checkout-form')) {
        initCheckout();
    }
});






