import './bootstrap';
import Alpine from 'alpinejs';

// Motion preference utilities
const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

const setMotionClass = (matches) => {
    document.documentElement.classList.toggle('reduce-motion', matches);
};

setMotionClass(motionQuery.matches);

const handleMotionChange = (event) => setMotionClass(event.matches);

if (typeof motionQuery.addEventListener === 'function') {
    motionQuery.addEventListener('change', handleMotionChange);
} else if (typeof motionQuery.addListener === 'function') {
    motionQuery.addListener(handleMotionChange);
}

window.shouldReduceMotion = () => document.documentElement.classList.contains('reduce-motion');
window.prefersReducedMotionQuery = motionQuery;

// Make Alpine available globally
window.Alpine = Alpine;

// Import Alpine components
import './components/search';
import './components/mobileNav';
import './components/filterSidebar';
import './components/lightbox';

// Start Alpine
Alpine.start();

// Lazy Loading Images
document.addEventListener('DOMContentLoaded', () => {
    // Lazy load images using Intersection Observer
    const lazyImages = document.querySelectorAll('img.lazy, img[loading="lazy"]');

    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    
                    // If image has data-src, use it
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    
                    img.classList.remove('lazy');
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '50px 0px',
            threshold: 0.01
        });

        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for browsers without IntersectionObserver
        lazyImages.forEach(img => {
            if (img.dataset.src) {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            }
        });
    }
});

// Page Transitions and Animations
document.addEventListener('DOMContentLoaded', () => {
    const reduceMotion = window.shouldReduceMotion();

    // Add fade-in animation to main content
    const mainContent = document.querySelector('main');
    if (mainContent && !reduceMotion) {
        mainContent.classList.add('animate-fade-in');
    }

    // Add staggered fade-in to cards
    if (!reduceMotion) {
        const cards = document.querySelectorAll('.card, .card-hover, article');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('animate-fade-in-up');
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: reduceMotion ? 'auto' : 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Add loading state to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = this.querySelector('button[type="submit"]');
            if (submitButton && !submitButton.disabled) {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-75', 'cursor-not-allowed');
                
                // Add loading spinner
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
                
                // Restore button after 10 seconds as fallback
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-75', 'cursor-not-allowed');
                    submitButton.innerHTML = originalText;
                }, 10000);
            }
        });
    });

    // Add hover scale effect to images
    const images = document.querySelectorAll('img[class*="group-hover:scale"]');
    images.forEach(img => {
        img.style.transition = 'transform 0.3s ease-in-out';
    });

    // Intersection Observer for scroll animations
    if (!reduceMotion) {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe sections for scroll animations
        const sections = document.querySelectorAll('section');
        sections.forEach(section => {
            observer.observe(section);
        });
    }

    // Add ripple effect to buttons
    document.querySelectorAll('.btn, .btn-primary, .btn-secondary, .btn-outline').forEach(button => {
        button.addEventListener('click', function(e) {
            if (window.shouldReduceMotion()) {
                return;
            }
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// Testimonial slider component
window.testimonialSlider = function() {
    return {
        active: 0,
        testimonials: [
            {
                quote: 'HEAC guided our Sukuk programme from concept to certification with exceptional governance and responsiveness.',
                name: 'Ayesha Rahman',
                role: 'Chief Compliance Officer, Unity Islamic',
                client: 'Unity Islamic',
                sector: 'Capital Markets'
            },
            {
                quote: 'Their scholars translated complex fintech use-cases into practical Shariah frameworks that regulators approved quickly.',
                name: 'Fahad Al-Mutairi',
                role: 'Founder & CEO, HalalPay',
                client: 'HalalPay',
                sector: 'Fintech'
            },
            {
                quote: 'From Zakat governance to executive training, HEAC has become a trusted partner across our global subsidiaries.',
                name: 'Dr. Sara Lim',
                role: 'Group Head of Ethics, Crescent Holdings',
                client: 'Crescent Holdings',
                sector: 'Corporate'
            }
        ],
        interval: null,
        prefersReduced: window.shouldReduceMotion(),
        init() {
            const query = window.prefersReducedMotionQuery;
            const updatePreference = (event) => {
                this.prefersReduced = event.matches;
                if (this.prefersReduced) {
                    this.stop();
                } else {
                    this.start();
                }
            };

            if (query) {
                if (typeof query.addEventListener === 'function') {
                    query.addEventListener('change', updatePreference);
                } else if (typeof query.addListener === 'function') {
                    query.addListener(updatePreference);
                }
            }

            if (!this.prefersReduced) {
                this.start();
            }
        },
        start() {
            if (this.prefersReduced) {
                return;
            }
            this.stop();
            this.interval = setInterval(() => {
                this.next(false);
            }, 7000);
        },
        stop() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        },
        next(restart = true) {
            this.active = (this.active + 1) % this.testimonials.length;
            if (restart && !this.prefersReduced) {
                this.start();
            }
        },
        prev() {
            this.active = (this.active - 1 + this.testimonials.length) % this.testimonials.length;
            if (!this.prefersReduced) {
                this.start();
            }
        },
        goTo(index) {
            this.active = index;
            if (!this.prefersReduced) {
                this.start();
            }
        }
    };
};

// Add loading indicator for page navigation
window.addEventListener('beforeunload', () => {
    if (window.shouldReduceMotion()) {
        return;
    }
    const loader = document.createElement('div');
    loader.className = 'fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex items-center justify-center';
    loader.innerHTML = `
        <div class="text-center">
            <svg class="animate-spin h-12 w-12 text-primary-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-neutral-600">Loading...</p>
        </div>
    `;
    document.body.appendChild(loader);
});
