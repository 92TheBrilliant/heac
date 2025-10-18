@extends('layouts.app')

@push('structured-data')
    <x-structured-data :data="$structuredData ?? []" />
@endpush

@section('content')
<div class="bg-white">
    {{-- Page Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl">
                {{-- Categories --}}
                @if($research->categories->count() > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($research->categories as $category)
                    <a href="{{ route('research.index', ['categories' => [$category->id]]) }}" 
                       class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-500 text-white hover:bg-blue-400 transition">
                        {{ $category->name }}
                    </a>
                    @endforeach
                </div>
                @endif

                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
                    {{ $research->title }}
                </h1>

                {{-- Meta Information --}}
                <div class="flex flex-wrap items-center gap-4 text-blue-100">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $research->publication_date->format('F d, Y') }}
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ $research->views_count }} views
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ $research->downloads_count }} downloads
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-4xl mx-auto">
            {{-- Authors/Scholars --}}
            @if($research->authors && count($research->authors) > 0)
            <div class="mb-8 pb-8 border-b border-gray-200">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                    Authors & Scholars
                </h2>
                <div class="flex flex-wrap gap-3">
                    @foreach($research->authors as $author)
                    <div class="flex items-center bg-gray-50 rounded-lg px-4 py-2">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold mr-3">
                            {{ substr(is_array($author) ? ($author['name'] ?? 'A') : $author, 0, 1) }}
                        </div>
                        <div>
                            <span class="text-gray-900 font-medium block">{{ is_array($author) ? $author['name'] : $author }}</span>
                            @if(is_array($author) && isset($author['affiliation']))
                            <span class="text-xs text-gray-500">{{ $author['affiliation'] }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Abstract --}}
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    Abstract
                </h2>
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    {{ $research->abstract }}
                </div>
            </div>

            {{-- Tags --}}
            @if($research->tags->count() > 0)
            <div class="mb-8">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                    Tags
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($research->tags as $tag)
                    <a href="{{ route('research.index', ['tags' => [$tag->id]]) }}" 
                       class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                        #{{ $tag->name }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Download Button --}}
            @if($research->file_path)
            <div class="mb-8 p-6 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                            Download Full Research
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ strtoupper($research->file_type) }} • {{ number_format($research->file_size / 1024 / 1024, 2) }} MB
                        </p>
                    </div>
                    <a href="{{ route('research.download', $research->slug) }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition whitespace-nowrap">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>
            @endif

            {{-- Social Sharing --}}
            <div class="mb-12 pb-8 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                    Share This Research
                </h3>
                <div class="flex flex-wrap gap-3">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('research.show', $research->slug)) }}" 
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                        </svg>
                        Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('research.show', $research->slug)) }}&text={{ urlencode($research->title) }}" 
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                        </svg>
                        Twitter
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('research.show', $research->slug)) }}&title={{ urlencode($research->title) }}" 
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                        </svg>
                        LinkedIn
                    </a>
                    <button onclick="copyToClipboard('{{ route('research.show', $research->slug) }}')" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Copy Link
                    </button>
                </div>
            </div>

            {{-- Related Publications --}}
            @if(isset($relatedResearch) && $relatedResearch->count() > 0)
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    Related Publications
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($relatedResearch as $related)
                    <article class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition">
                        @if($related->thumbnail)
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                            <img src="{{ Storage::url($related->thumbnail) }}" alt="{{ $related->title }}" class="w-full h-32 object-cover">
                        </div>
                        @endif
                        
                        <div class="p-4">
                            <div class="flex items-center gap-2 mb-2">
                                @if($related->categories->count() > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $related->categories->first()->name }}
                                </span>
                                @endif
                                <span class="text-xs text-gray-500">
                                    {{ $related->publication_date->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-blue-600 transition line-clamp-2">
                                <a href="{{ route('research.show', $related->slug) }}">
                                    {{ $related->title }}
                                </a>
                            </h3>
                            
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                {{ $related->abstract }}
                            </p>
                            
                            <a href="{{ route('research.show', $related->slug) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm inline-flex items-center">
                                Read More
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}

// Track research view on page load
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.trackResearchView === 'function') {
        window.trackResearchView({{ $research->id }}, '{{ addslashes($research->title) }}');
    }
});

// Track download button clicks
document.querySelectorAll('a[href*="research.download"]').forEach(function(link) {
    link.addEventListener('click', function() {
        if (typeof window.trackResearchDownload === 'function') {
            window.trackResearchDownload({{ $research->id }}, '{{ addslashes($research->title) }}');
        }
    });
});
</script>
@endpush
@endsection
