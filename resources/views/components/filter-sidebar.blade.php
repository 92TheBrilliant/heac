@props([
    'categories' => [],
    'tags' => [],
    'years' => []
])

<!-- Hidden data for Alpine.js -->
<script id="filters-data" type="application/json">
{!! json_encode([
    'categories' => $categories,
    'tags' => $tags,
    'years' => $years
]) !!}
</script>

<div x-data="filterSidebar()" class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Filter Header -->
    <div class="px-4 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
            <button 
                x-show="hasActiveFilters()"
                @click="clearFilters"
                class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors"
            >
                Clear all
                <span 
                    x-show="getActiveFilterCount() > 0"
                    class="ml-1 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-blue-600 rounded-full"
                    x-text="getActiveFilterCount()"
                ></span>
            </button>
        </div>
    </div>

    <!-- Filter Groups -->
    <div class="divide-y divide-gray-200">
        
        <!-- Categories Filter -->
        <div class="px-4 py-4">
            <button 
                @click="toggleGroup('categories')"
                class="flex items-center justify-between w-full text-left"
            >
                <h4 class="text-sm font-medium text-gray-900">Categories</h4>
                <svg 
                    class="w-5 h-5 text-gray-400 transition-transform"
                    :class="{ 'rotate-180': !collapsedGroups.categories }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div 
                x-show="!collapsedGroups.categories"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mt-3 space-y-2"
            >
                @forelse($categories as $category)
                    <label class="flex items-center cursor-pointer group">
                        <input 
                            type="checkbox"
                            :checked="isCategorySelected({{ $category->id }})"
                            @change="toggleCategory({{ $category->id }})"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        />
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900 flex-1">
                            {{ $category->name }}
                        </span>
                        <span class="ml-2 text-xs text-gray-500">
                            {{ $category->research_count ?? 0 }}
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-gray-500">No categories available</p>
                @endforelse
            </div>
        </div>

        <!-- Tags Filter -->
        <div class="px-4 py-4">
            <button 
                @click="toggleGroup('tags')"
                class="flex items-center justify-between w-full text-left"
            >
                <h4 class="text-sm font-medium text-gray-900">Tags</h4>
                <svg 
                    class="w-5 h-5 text-gray-400 transition-transform"
                    :class="{ 'rotate-180': !collapsedGroups.tags }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div 
                x-show="!collapsedGroups.tags"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mt-3 space-y-2 max-h-64 overflow-y-auto"
            >
                @forelse($tags as $tag)
                    <label class="flex items-center cursor-pointer group">
                        <input 
                            type="checkbox"
                            :checked="isTagSelected({{ $tag->id }})"
                            @change="toggleTag({{ $tag->id }})"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        />
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900 flex-1">
                            {{ $tag->name }}
                        </span>
                        <span class="ml-2 text-xs text-gray-500">
                            {{ $tag->research_count ?? 0 }}
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-gray-500">No tags available</p>
                @endforelse
            </div>
        </div>

        <!-- Year Filter -->
        <div class="px-4 py-4">
            <button 
                @click="toggleGroup('year')"
                class="flex items-center justify-between w-full text-left"
            >
                <h4 class="text-sm font-medium text-gray-900">Publication Year</h4>
                <svg 
                    class="w-5 h-5 text-gray-400 transition-transform"
                    :class="{ 'rotate-180': !collapsedGroups.year }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div 
                x-show="!collapsedGroups.year"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mt-3 space-y-2"
            >
                <label class="flex items-center cursor-pointer group">
                    <input 
                        type="radio"
                        name="year"
                        :checked="filters.year === ''"
                        @change="setYear('')"
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                        All Years
                    </span>
                </label>

                @foreach($years as $year)
                    <label class="flex items-center cursor-pointer group">
                        <input 
                            type="radio"
                            name="year"
                            :checked="filters.year === '{{ $year->year }}'"
                            @change="setYear('{{ $year->year }}')"
                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                        />
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900 flex-1">
                            {{ $year->year }}
                        </span>
                        <span class="ml-2 text-xs text-gray-500">
                            {{ $year->count }}
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Sort Filter -->
        <div class="px-4 py-4">
            <button 
                @click="toggleGroup('sort')"
                class="flex items-center justify-between w-full text-left"
            >
                <h4 class="text-sm font-medium text-gray-900">Sort By</h4>
                <svg 
                    class="w-5 h-5 text-gray-400 transition-transform"
                    :class="{ 'rotate-180': !collapsedGroups.sort }"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <div 
                x-show="!collapsedGroups.sort"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mt-3 space-y-2"
            >
                <label class="flex items-center cursor-pointer group">
                    <input 
                        type="radio"
                        name="sort"
                        :checked="filters.sort === 'latest'"
                        @change="setSort('latest')"
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                        Latest First
                    </span>
                </label>

                <label class="flex items-center cursor-pointer group">
                    <input 
                        type="radio"
                        name="sort"
                        :checked="filters.sort === 'oldest'"
                        @change="setSort('oldest')"
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                        Oldest First
                    </span>
                </label>

                <label class="flex items-center cursor-pointer group">
                    <input 
                        type="radio"
                        name="sort"
                        :checked="filters.sort === 'popular'"
                        @change="setSort('popular')"
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                        Most Popular
                    </span>
                </label>

                <label class="flex items-center cursor-pointer group">
                    <input 
                        type="radio"
                        name="sort"
                        :checked="filters.sort === 'title'"
                        @change="setSort('title')"
                        class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
                        Title (A-Z)
                    </span>
                </label>
            </div>
        </div>

    </div>

    <!-- Active Filters Display -->
    <div x-show="hasActiveFilters()" class="px-4 py-4 bg-gray-50 border-t border-gray-200">
        <h5 class="text-xs font-medium text-gray-700 uppercase tracking-wider mb-2">Active Filters</h5>
        <div class="flex flex-wrap gap-2">
            <!-- Category badges -->
            <template x-for="categoryId in filters.categories" :key="'cat-' + categoryId">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <span x-text="availableFilters.categories.find(c => c.id === categoryId)?.name || 'Category'"></span>
                    <button 
                        @click="toggleCategory(categoryId)"
                        class="ml-1.5 inline-flex items-center justify-center w-4 h-4 text-blue-600 hover:text-blue-800"
                    >
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
            </template>

            <!-- Tag badges -->
            <template x-for="tagId in filters.tags" :key="'tag-' + tagId">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <span x-text="availableFilters.tags.find(t => t.id === tagId)?.name || 'Tag'"></span>
                    <button 
                        @click="toggleTag(tagId)"
                        class="ml-1.5 inline-flex items-center justify-center w-4 h-4 text-green-600 hover:text-green-800"
                    >
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
            </template>

            <!-- Year badge -->
            <span x-show="filters.year" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                <span x-text="filters.year"></span>
                <button 
                    @click="setYear('')"
                    class="ml-1.5 inline-flex items-center justify-center w-4 h-4 text-purple-600 hover:text-purple-800"
                >
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </span>
        </div>
    </div>
</div>
