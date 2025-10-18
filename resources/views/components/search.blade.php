<div x-data="searchComponent" class="relative w-full max-w-md">
    <!-- Search Input -->
    <div class="relative">
        <input 
            type="text" 
            x-model="query"
            @input="search"
            @keydown="handleKeydown"
            @focus="query.length >= 2 && results.length > 0 ? isOpen = true : null"
            placeholder="Search research, pages..."
            class="w-full px-4 py-2 pl-10 pr-10 text-gray-900 placeholder-gray-500 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
        
        <!-- Search Icon -->
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <!-- Loading Spinner -->
        <div x-show="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-3">
            <svg class="w-5 h-5 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Clear Button -->
        <button 
            x-show="query.length > 0 && !isLoading"
            @click="clearSearch"
            type="button"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Search Results Dropdown -->
    <div 
        x-show="isOpen && results.length > 0"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        x-ref="dropdown"
        class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg max-h-96 overflow-y-auto"
        style="display: none;"
    >
        <ul class="py-2">
            <template x-for="(result, index) in results" :key="result.id">
                <li>
                    <a 
                        :href="result.url"
                        @mouseenter="selectResult(index)"
                        :data-selected="selectedIndex === index"
                        class="block px-4 py-3 hover:bg-gray-50 transition-colors"
                        :class="{ 'bg-blue-50': selectedIndex === index }"
                    >
                        <div class="flex items-start">
                            <!-- Type Icon -->
                            <div class="flex-shrink-0 mt-1">
                                <svg x-show="result.type === 'research'" class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <svg x-show="result.type === 'page'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>

                            <!-- Content -->
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900" x-text="result.title"></p>
                                    <span 
                                        class="ml-2 px-2 py-1 text-xs font-medium rounded-full"
                                        :class="{
                                            'bg-blue-100 text-blue-800': result.type === 'research',
                                            'bg-green-100 text-green-800': result.type === 'page'
                                        }"
                                        x-text="result.type"
                                    ></span>
                                </div>
                                <p x-show="result.excerpt" class="mt-1 text-sm text-gray-500 line-clamp-2" x-text="result.excerpt"></p>
                            </div>
                        </div>
                    </a>
                </li>
            </template>
        </ul>

        <!-- View All Results Link -->
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            <a 
                :href="`/search?q=${encodeURIComponent(query)}`"
                class="text-sm font-medium text-blue-600 hover:text-blue-800"
            >
                View all results for "<span x-text="query"></span>"
            </a>
        </div>
    </div>

    <!-- No Results Message -->
    <div 
        x-show="isOpen && !isLoading && results.length === 0 && query.length >= 2"
        x-transition
        class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg p-4"
        style="display: none;"
    >
        <p class="text-sm text-gray-500 text-center">No results found for "<span x-text="query"></span>"</p>
    </div>
</div>
