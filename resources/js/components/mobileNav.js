import Alpine from 'alpinejs';

Alpine.data('mobileNav', () => ({
    isOpen: false,

    init() {
        // Watch for open state changes to handle body scroll locking
        this.$watch('isOpen', value => {
            if (value) {
                this.lockScroll();
            } else {
                this.unlockScroll();
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        // Close menu on window resize if it becomes desktop size
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768 && this.isOpen) {
                this.close();
            }
        });
    },

    toggle() {
        this.isOpen = !this.isOpen;
    },

    open() {
        this.isOpen = true;
    },

    close() {
        this.isOpen = false;
    },

    lockScroll() {
        // Save current scroll position
        const scrollY = window.scrollY;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollY}px`;
        document.body.style.width = '100%';
        document.body.style.overflow = 'hidden';
    },

    unlockScroll() {
        // Restore scroll position
        const scrollY = document.body.style.top;
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflow = '';
        window.scrollTo(0, parseInt(scrollY || '0') * -1);
    }
}));
