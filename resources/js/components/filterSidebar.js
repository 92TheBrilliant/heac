import Alpine from 'alpinejs';

Alpine.data('filterSidebar', (initialFilters = {}) => ({
    filters: {
        categories: initialFilters.categories || [],
        tags: initialFilters.tags || [],
        year: initialFilters.year || '',
        sort: initialFilters.sort || 'latest'
    },
    
    collapsedGroups: {
        categories: false,
        tags: false,
        year: false,
        sort: false
    },

    availableFilters: {
        categories: [],
        tags: [],
        years: []
    },

    init() {
        // Load available filters from the page data
        this.loadAvailableFilters();
        
        // Apply filters from URL on page load
        this.loadFiltersFromUrl();
    },

    loadAvailableFilters() {
        // Get filter data from data attributes or window object
        const filtersData = document.getElementById('filters-data');
        if (filtersData) {
            try {
                const data = JSON.parse(filtersData.textContent);
                this.availableFilters = data;
            } catch (e) {
                console.error('Error parsing filters data:', e);
            }
        }
    },

    loadFiltersFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Load categories
        const categories = urlParams.getAll('categories[]');
        if (categories.length > 0) {
            this.filters.categories = categories.map(id => parseInt(id));
        }
        
        // Load tags
        const tags = urlParams.getAll('tags[]');
        if (tags.length > 0) {
            this.filters.tags = tags.map(id => parseInt(id));
        }
        
        // Load year
        const year = urlParams.get('year');
        if (year) {
            this.filters.year = year;
        }
        
        // Load sort
        const sort = urlParams.get('sort');
        if (sort) {
            this.filters.sort = sort;
        }
    },

    toggleGroup(group) {
        this.collapsedGroups[group] = !this.collapsedGroups[group];
    },

    toggleCategory(categoryId) {
        const index = this.filters.categories.indexOf(categoryId);
        if (index > -1) {
            this.filters.categories.splice(index, 1);
        } else {
            this.filters.categories.push(categoryId);
        }
        this.applyFilters();
    },

    toggleTag(tagId) {
        const index = this.filters.tags.indexOf(tagId);
        if (index > -1) {
            this.filters.tags.splice(index, 1);
        } else {
            this.filters.tags.push(tagId);
        }
        this.applyFilters();
    },

    setYear(year) {
        this.filters.year = year;
        this.applyFilters();
    },

    setSort(sort) {
        this.filters.sort = sort;
        this.applyFilters();
    },

    isCategorySelected(categoryId) {
        return this.filters.categories.includes(categoryId);
    },

    isTagSelected(tagId) {
        return this.filters.tags.includes(tagId);
    },

    clearFilters() {
        this.filters = {
            categories: [],
            tags: [],
            year: '',
            sort: 'latest'
        };
        this.applyFilters();
    },

    hasActiveFilters() {
        return this.filters.categories.length > 0 || 
               this.filters.tags.length > 0 || 
               this.filters.year !== '';
    },

    getActiveFilterCount() {
        let count = 0;
        count += this.filters.categories.length;
        count += this.filters.tags.length;
        if (this.filters.year) count++;
        return count;
    },

    applyFilters() {
        // Build URL with filters
        const url = new URL(window.location.href);
        const params = new URLSearchParams();

        // Add categories
        this.filters.categories.forEach(id => {
            params.append('categories[]', id);
        });

        // Add tags
        this.filters.tags.forEach(id => {
            params.append('tags[]', id);
        });

        // Add year
        if (this.filters.year) {
            params.set('year', this.filters.year);
        }

        // Add sort
        if (this.filters.sort && this.filters.sort !== 'latest') {
            params.set('sort', this.filters.sort);
        }

        // Update URL and reload
        url.search = params.toString();
        window.location.href = url.toString();
    },

    getCategoryCount(categoryId) {
        const category = this.availableFilters.categories.find(c => c.id === categoryId);
        return category ? category.count : 0;
    },

    getTagCount(tagId) {
        const tag = this.availableFilters.tags.find(t => t.id === tagId);
        return tag ? tag.count : 0;
    }
}));
