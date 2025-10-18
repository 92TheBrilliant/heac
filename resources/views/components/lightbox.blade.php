@props(['images' => []])

<div 
    x-data="lightbox(@js($images))"
    @open-lightbox.window="images = $event.detail.images; open($event.detail.index)"
>
    <!-- Lightbox Modal -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center"
        style="display: none;"
        x-cloak
    >
        <!-- Backdrop -->
        <div 
            @click="close"
            class="absolute inset-0 bg-black bg-opacity-90"
        ></div>

        <!-- Modal Content -->
        <div class="relative z-10 w-full h-full flex flex-col">
            
            <!-- Header -->
            <div class="flex items-center justify-between px-4 py-4 bg-black bg-opacity-50">
                <div class="flex-1">
                    <h3 
                        x-show="currentImage.title"
                        x-text="currentImage.title"
                        class="text-lg font-semibold text-white"
                    ></h3>
                    <p 
                        x-show="hasMultipleImages"
                        class="text-sm text-gray-300"
                    >
                        <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                    </p>
                </div>
                
                <!-- Close Button -->
                <button 
                    @click="close"
                    class="p-2 text-white hover:text-gray-300 transition-colors"
                    aria-label="Close lightbox"
                >
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Image Container -->
            <div class="flex-1 flex items-center justify-center px-4 py-8 relative">
                
                <!-- Previous Button -->
                <button 
                    x-show="hasPrevious"
                    @click="previous"
                    class="absolute left-4 p-3 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full transition-all z-20"
                    aria-label="Previous image"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <!-- Image -->
                <div class="max-w-7xl max-h-full flex items-center justify-center">
                    <img 
                        :src="currentImage.src"
                        :alt="currentImage.alt"
                        class="max-w-full max-h-full object-contain"
                        @click.stop
                    />
                </div>

                <!-- Next Button -->
                <button 
                    x-show="hasNext"
                    @click="next"
                    class="absolute right-4 p-3 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full transition-all z-20"
                    aria-label="Next image"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- Description -->
            <div 
                x-show="currentImage.description"
                class="px-4 py-4 bg-black bg-opacity-50"
            >
                <p 
                    x-text="currentImage.description"
                    class="text-sm text-gray-300 text-center max-w-3xl mx-auto"
                ></p>
            </div>

            <!-- Thumbnail Navigation -->
            <div 
                x-show="hasMultipleImages && images.length <= 10"
                class="px-4 py-4 bg-black bg-opacity-50 overflow-x-auto"
            >
                <div class="flex justify-center gap-2">
                    <template x-for="(image, index) in images" :key="index">
                        <button 
                            @click="goTo(index)"
                            class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all"
                            :class="{
                                'border-white': currentIndex === index,
                                'border-transparent opacity-60 hover:opacity-100': currentIndex !== index
                            }"
                        >
                            <img 
                                :src="image.thumbnail"
                                :alt="image.alt"
                                class="w-full h-full object-cover"
                            />
                        </button>
                    </template>
                </div>
            </div>

            <!-- Keyboard Hints -->
            <div class="px-4 py-2 bg-black bg-opacity-50 text-center">
                <p class="text-xs text-gray-400">
                    Use arrow keys to navigate • Press ESC to close
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
