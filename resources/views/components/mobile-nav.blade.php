@props(['navigation' => []])

<div x-data="mobileNav" class="md:hidden">
    <!-- Hamburger Button -->
    <button 
        @click="toggle"
        type="button"
        class="inline-flex items-center justify-center p-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors"
        aria-expanded="false"
        :aria-expanded="isOpen.toString()"
    >
        <span class="sr-only">Open main menu</span>
        
        <!-- Hamburger Icon (shown when menu is closed) -->
        <svg 
            x-show="!isOpen"
            class="w-6 h-6" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
        
        <!-- Close Icon (shown when menu is open) -->
        <svg 
            x-show="isOpen"
            x-cloak
            class="w-6 h-6" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <!-- Mobile Menu Overlay -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close"
        class="fixed inset-0 z-40 bg-black bg-opacity-50"
        x-cloak
    ></div>

    <!-- Mobile Menu Panel -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="-translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-full max-w-sm bg-white shadow-xl overflow-y-auto"
        x-cloak
    >
        <!-- Menu Header -->
        <div class="flex items-center justify-between px-4 py-5 border-b border-gray-200">
            <div class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="HEAC Logo" class="h-8 w-auto" onerror="this.style.display='none'">
                <span class="ml-2 text-xl font-bold text-gray-900">HEAC</span>
            </div>
            <button 
                @click="close"
                type="button"
                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
            >
                <span class="sr-only">Close menu</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="px-4 py-6 space-y-1">
            <a 
                href="{{ route('home') }}" 
                class="block px-4 py-3 text-base font-medium text-gray-900 hover:bg-gray-50 rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700' : '' }}"
                @click="close"
            >
                Home
            </a>
            
            <a 
                href="{{ route('research.index') }}" 
                class="block px-4 py-3 text-base font-medium text-gray-900 hover:bg-gray-50 rounded-lg transition-colors {{ request()->routeIs('research.*') ? 'bg-blue-50 text-blue-700' : '' }}"
                @click="close"
            >
                Research
            </a>

            @foreach($navigation as $item)
                <a 
                    href="{{ $item['url'] }}" 
                    class="block px-4 py-3 text-base font-medium text-gray-900 hover:bg-gray-50 rounded-lg transition-colors"
                    @click="close"
                >
                    {{ $item['title'] }}
                </a>
            @endforeach
            
            <a 
                href="{{ route('contact.index') }}" 
                class="block px-4 py-3 text-base font-medium text-gray-900 hover:bg-gray-50 rounded-lg transition-colors {{ request()->routeIs('contact.*') ? 'bg-blue-50 text-blue-700' : '' }}"
                @click="close"
            >
                Contact
            </a>
        </nav>

        <!-- Mobile Search -->
        <div class="px-4 py-4 border-t border-gray-200">
            <x-search />
        </div>

        <!-- Additional Links -->
        <div class="px-4 py-6 border-t border-gray-200">
            <div class="space-y-2">
                <a 
                    href="/admin" 
                    class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Admin Login
                </a>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="px-4 py-6 border-t border-gray-200 bg-gray-50">
            <p class="text-xs text-gray-500 text-center">
                © {{ date('Y') }} Higher Education Accreditation Commission
            </p>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
