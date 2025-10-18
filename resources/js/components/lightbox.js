import Alpine from 'alpinejs';

Alpine.data('lightbox', (images = []) => ({
    images: images,
    currentIndex: 0,
    isOpen: false,

    init() {
        // Handle keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!this.isOpen) return;

            switch (e.key) {
                case 'Escape':
                    this.close();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    this.previous();
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    this.next();
                    break;
            }
        });

        // Watch for open state changes to handle body scroll locking
        this.$watch('isOpen', value => {
            if (value) {
                this.lockScroll();
            } else {
                this.unlockScroll();
            }
        });
    },

    open(index = 0) {
        this.currentIndex = index;
        this.isOpen = true;
    },

    close() {
        this.isOpen = false;
    },

    next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
    },

    previous() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
    },

    goTo(index) {
        this.currentIndex = index;
    },

    get currentImage() {
        return this.images[this.currentIndex] || {};
    },

    get hasMultipleImages() {
        return this.images.length > 1;
    },

    get hasPrevious() {
        return this.hasMultipleImages;
    },

    get hasNext() {
        return this.hasMultipleImages;
    },

    lockScroll() {
        const scrollY = window.scrollY;
        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollY}px`;
        document.body.style.width = '100%';
        document.body.style.overflow = 'hidden';
    },

    unlockScroll() {
        const scrollY = document.body.style.top;
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflow = '';
        window.scrollTo(0, parseInt(scrollY || '0') * -1);
    }
}));

// Gallery component for managing multiple images
Alpine.data('gallery', () => ({
    images: [],

    init() {
        // Collect all gallery images from the DOM
        this.collectImages();
    },

    collectImages() {
        const galleryItems = this.$el.querySelectorAll('[data-gallery-item]');
        this.images = Array.from(galleryItems).map(item => ({
            src: item.dataset.src || item.src,
            thumbnail: item.dataset.thumbnail || item.src,
            alt: item.alt || '',
            title: item.dataset.title || '',
            description: item.dataset.description || ''
        }));
    },

    openLightbox(index) {
        // Dispatch event to open lightbox
        this.$dispatch('open-lightbox', { images: this.images, index: index });
    }
}));
