@extends('layouts.app')

@section('content')
<div class="bg-white">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Resources & Publications
            </h1>
            <p class="text-xl text-blue-100 max-w-3xl">
                Explore our comprehensive collection of Islamic finance research, fatwas, whitepapers, and industry insights
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Filter Sidebar --}}
            <aside class="lg:w-80 flex-shrink-0" x-data="{ 
                showCategories: true, 
                showTags: true,
                selectedCategories: {{ json_encode(request('categories', [])) }},
                selectedTags: {{ json_encode(request('tags', [])) }}
            }">
                <div class="bg-gray-50 rounded-lg p-6 sticky top-20">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Filters
                        </h2>
                        @if(request()->hasAny(['search', 'categories', 'tags', 'year']))
                        <a href="{{ route('research.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                            Clear All
                        </a>
                        @endif
                    </div>

                    {{-- Search --}}
                    <div class="mb-6">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            Search
                        </label>
                        <form action="{{ route('research.index') }}" method="GET" id="searchForm">
                            <input type="text" 
                                   name="search" 
                                   id="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search research..."
                                   class="form-input"
                                   x-on:input.debounce.500ms="$el.form.submit()">
                        </form>
                    </div>

                    {{-- Categories Filter --}}
                    @if(isset($categories) && $categories->count() > 0)
                    <div class="mb-6">
                        <button @click="showCategories = !showCategories" class="flex items-center justify-between w-full text-sm font-medium text-gray-700 mb-2">
                            <span>Categories</span>
                            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': !showCategories }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="showCategories" x-collapse class="space-y-2">
                            @foreach($categories as $category)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       value="{{ $category->id }}"
                                       {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       onchange="this.form.submit()"
                                       form="filterForm">
                                <span class="ml-2 text-sm text-gray-700">
                                    {{ $category->name }}
                                    <span class="text-gray-500">({{ $category->research_count ?? 0 }})</span>
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Tags Filter --}}
                    @if(isset($tags) && $tags->count() > 0)
                    <div class="mb-6">
                        <button @click="showTags = !showTags" class="flex items-center justify-between w-full text-sm font-medium text-gray-700 mb-2">
                            <span>Tags</span>
                            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': !showTags }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="showTags" x-collapse class="space-y-2">
                            @foreach($tags as $tag)
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       value="{{ $tag->id }}"
                                       {{ in_array($tag->id, request('tags', [])) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       onchange="this.form.submit()"
                                       form="filterForm">
                                <span class="ml-2 text-sm text-gray-700">
                                    {{ $tag->name }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Year Filter --}}
                    @if(isset($years) && count($years) > 0)
                    <div class="mb-6">
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                            Publication Year
                        </label>
                        <select name="year" 
                                id="year" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                onchange="this.form.submit()"
                                form="filterForm">
                            <option value="">All Years</option>
                            @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Hidden form for filters --}}
                    <form action="{{ route('research.index') }}" method="GET" id="filterForm" class="hidden">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="view" value="{{ request('view', 'grid') }}">
                    </form>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="flex-1">
                {{-- Toolbar --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                    <div class="text-gray-600">
                        Showing {{ $research->firstItem() ?? 0 }} - {{ $research->lastItem() ?? 0 }} of {{ $research->total() }} results
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- Sort --}}
                        <select name="sort" 
                                class="px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm"
                                onchange="window.location.href = '{{ route('research.index') }}?' + new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), sort: this.value}).toString()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                        </select>

                        {{-- View Toggle --}}
                        <div class="flex border border-gray-300 rounded-md overflow-hidden">
                            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" 
                               class="px-3 py-2 {{ request('view', 'grid') == 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" 
                               class="px-3 py-2 {{ request('view') == 'list' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Research Grid/List --}}
                @if($research->count() > 0)
                    @if(request('view') == 'list')
                        {{-- List View --}}
                        <div class="space-y-6">
                            @foreach($research as $item)
                            <article class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition flex flex-col sm:flex-row">
                                @if($item->thumbnail)
                                <div class="sm:w-48 flex-shrink-0">
                                    <img src="{{ Storage::url($item->thumbnail) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                                </div>
                                @endif
                                
                                <div class="p-6 flex-1">
                                    <div class="flex items-center gap-2 mb-3">
                                        @if($item->categories->count() > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item->categories->first()->name }}
                                        </span>
                                        @endif
                                        <span class="text-sm text-gray-500">
                                            {{ $item->publication_date->format('M d, Y') }}
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2 hover:text-blue-600 transition">
                                        <a href="{{ route('research.show', $item->slug) }}">
                                            {{ $item->title }}
                                        </a>
                                    </h3>
                                    
                                    <p class="text-gray-600 mb-4 line-clamp-2">
                                        {{ $item->abstract }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('research.show', $item->slug) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm inline-flex items-center">
                                            Read More
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                        <div class="flex items-center gap-3 text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                {{ $item->views_count }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                {{ $item->downloads_count }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                            @endforeach
                        </div>
                    @else
                        {{-- Grid View --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($research as $item)
                            <article class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition group">
                                @if($item->thumbnail)
                                <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                                    <img src="{{ Storage::url($item->thumbnail) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                                </div>
                                @else
                                <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                @endif
                                
                                <div class="p-6">
                                    <div class="flex items-center gap-2 mb-3">
                                        @if($item->categories->count() > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item->categories->first()->name }}
                                        </span>
                                        @endif
                                        <span class="text-sm text-gray-500">
                                            {{ $item->publication_date->format('M d, Y') }}
                                        </span>
                                    </div>
                                    
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition line-clamp-2">
                                        <a href="{{ route('research.show', $item->slug) }}">
                                            {{ $item->title }}
                                        </a>
                                    </h3>
                                    
                                    <p class="text-gray-600 mb-4 line-clamp-3">
                                        {{ $item->abstract }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('research.show', $item->slug) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm inline-flex items-center">
                                            Read More
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                        <div class="flex items-center gap-3 text-sm text-gray-500">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                {{ $item->views_count }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                {{ $item->downloads_count }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $research->links() }}
                    </div>
                @else
                    {{-- No Results --}}
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No publications found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria, or check back later for new content</p>
                        <div class="mt-6">
                            <a href="{{ route('research.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Clear Filters
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
