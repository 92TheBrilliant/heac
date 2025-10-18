@props([
    'images' => [],
    'columns' => 3,
    'gap' => 4
])

<div 
    x-data="gallery"
    class="grid gap-{{ $gap }}"
    style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr));"
>
    @foreach($images as $index => $image)
        <div class="relative group cursor-pointer overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow">
            <img 
                src="{{ $image['thumbnail'] ?? $image['src'] }}"
                alt="{{ $image['alt'] ?? '' }}"
                data-gallery-item
                data-src="{{ $image['src'] }}"
                data-thumbnail="{{ $image['thumbnail'] ?? $image['src'] }}"
                data-title="{{ $image['title'] ?? '' }}"
                data-description="{{ $image['description'] ?? '' }}"
                @click="openLightbox({{ $index }})"
                class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                loading="lazy"
            />
            
            <!-- Overlay on hover -->
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"></path>
                </svg>
            </div>

            <!-- Image Title -->
            @if(!empty($image['title']))
                <div class="absolute bottom-0 left-0 right-0 px-3 py-2 bg-gradient-to-t from-black to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <p class="text-sm font-medium text-white truncate">{{ $image['title'] }}</p>
                </div>
            @endif
        </div>
    @endforeach
</div>

<!-- Include lightbox component -->
<x-lightbox />
