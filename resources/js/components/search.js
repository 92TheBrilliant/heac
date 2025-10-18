import Alpine from 'alpinejs';

Alpine.data('searchComponent', () => ({
    query: '',
    results: [],
    isOpen: false,
    isLoading: false,
    selectedIndex: -1,
    debounceTimer: null,

    init() {
        // Close dropdown when clicking outside
        this.$watch('isOpen', value => {
            if (value) {
                document.addEventListener('click', this.handleClickOutside.bind(this));
            } else {
                document.removeEventListener('click', this.handleClickOutside.bind(this));
            }
        });
    },

    handleClickOutside(event) {
        if (!this.$el.contains(event.target)) {
            this.isOpen = false;
        }
    },

    search() {
        // Clear previous timer
        clearTimeout(this.debounceTimer);

        // If query is empty, close dropdown
        if (this.query.trim().length === 0) {
            this.isOpen = false;
            this.results = [];
            return;
        }

        // Debounce the search (300ms delay)
        this.debounceTimer = setTimeout(() => {
            this.performSearch();
        }, 300);
    },

    async performSearch() {
        if (this.query.trim().length < 2) {
            return;
        }

        this.isLoading = true;
        this.selectedIndex = -1;

        try {
            const response = await fetch(`/api/search?q=${encodeURIComponent(this.query)}`);
            const data = await response.json();
            
            this.results = data.results || [];
            this.isOpen = this.results.length > 0;
        } catch (error) {
            console.error('Search error:', error);
            this.results = [];
        } finally {
            this.isLoading = false;
        }
    },

    handleKeydown(event) {
        if (!this.isOpen) return;

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.results.length - 1);
                this.scrollToSelected();
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                this.scrollToSelected();
                break;
            case 'Enter':
                event.preventDefault();
                if (this.selectedIndex >= 0 && this.results[this.selectedIndex]) {
                    window.location.href = this.results[this.selectedIndex].url;
                }
                break;
            case 'Escape':
                this.isOpen = false;
                this.selectedIndex = -1;
                break;
        }
    },

    scrollToSelected() {
        this.$nextTick(() => {
            const selected = this.$refs.dropdown?.querySelector('[data-selected="true"]');
            if (selected) {
                selected.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        });
    },

    selectResult(index) {
        this.selectedIndex = index;
    },

    clearSearch() {
        this.query = '';
        this.results = [];
        this.isOpen = false;
        this.selectedIndex = -1;
    }
}));
