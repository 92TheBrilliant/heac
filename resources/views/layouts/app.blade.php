<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>{{ $metaTitle ?? config('app.name', 'HEAC') }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Higher Education Accreditation Commission' }}">
    <meta name="keywords" content="{{ $metaKeywords ?? '' }}">
    
    {{-- Open Graph Tags --}}
    <meta property="og:title" content="{{ $ogTitle ?? $metaTitle ?? config('app.name') }}">
    <meta property="og:description" content="{{ $ogDescription ?? $metaDescription ?? '' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('images/og-default.jpg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    
    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle ?? $metaTitle ?? config('app.name') }}">
    <meta name="twitter:description" content="{{ $ogDescription ?? $metaDescription ?? '' }}">
    <meta name="twitter:image" content="{{ $ogImage ?? asset('images/og-default.jpg') }}">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
    
    {{-- Structured Data --}}
    @stack('structured-data')

    {{-- Google Analytics --}}
    <x-google-analytics />
</head>
<body class="font-sans antialiased bg-gray-50">
    {{-- Header --}}
    <header class="bg-white shadow-soft sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        <nav class="container-custom">
            <div class="flex justify-between items-center h-16 md:h-18">
                {{-- Logo --}}
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="flex items-center touch-manipulation">
                        <img src="{{ asset('images/logo.svg') }}" alt="HEAC Logo" class="h-8 sm:h-10 w-auto">
                        <span class="ml-2 sm:ml-3 text-lg sm:text-xl font-bold text-gray-900 hidden xs:block">HEAC</span>
                    </a>
                </div>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex md:items-center md:space-x-6 lg:space-x-8">
                    <a href="{{ route('home') }}" class="link-muted px-3 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'text-primary-600 border-b-2 border-primary-600' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('page.show', 'about') }}" class="link-muted px-3 py-2 text-sm font-medium {{ request()->routeIs('page.show') && request()->route('slug') == 'about' ? 'text-primary-600 border-b-2 border-primary-600' : '' }}">
                        About Us
                    </a>
                    <a href="{{ route('page.show', 'services') }}" class="link-muted px-3 py-2 text-sm font-medium {{ request()->routeIs('page.show') && request()->route('slug') == 'services' ? 'text-primary-600 border-b-2 border-primary-600' : '' }}">
                        Services
                    </a>
                    <a href="{{ route('page.show', 'training') }}" class="link-muted px-3 py-2 text-sm font-medium {{ request()->routeIs('page.show') && request()->route('slug') == 'training' ? 'text-primary-600 border-b-2 border-primary-600' : '' }}">
                        Training & Events
                    </a>
                    <a href="{{ route('research.index') }}" class="link-muted px-3 py-2 text-sm font-medium {{ request()->routeIs('research.*') ? 'text-primary-600 border-b-2 border-primary-600' : '' }}">
                        Resources
                    </a>
                    <a href="{{ route('page.show', 'team') }}" class="link-muted px-3 py-2 text-sm font-medium {{ request()->routeIs('page.show') && request()->route('slug') == 'team' ? 'text-primary-600 border-b-2 border-primary-600' : '' }}">
                        Team
                    </a>
                    <a href="{{ route('contact.index') }}" class="btn-primary btn-sm">
                        Contact Us
                    </a>
                    
                    {{-- Language Switcher --}}
                    <x-language-switcher />
                </div>

                {{-- Mobile menu button --}}
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="p-2 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 touch-manipulation">
                        <span class="sr-only">Open main menu</span>
                        <svg x-show="!mobileMenuOpen" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenuOpen" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Navigation --}}
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="md:hidden pb-4"
                 style="display: none;"
                 @click.away="mobileMenuOpen = false">
                <div class="space-y-1 pt-2">
                    <a href="{{ route('home') }}" class="block px-4 py-3 text-base font-medium rounded-lg touch-manipulation {{ request()->routeIs('home') ? 'text-primary-600 bg-primary-50' : 'text-gray-700 hover:bg-gray-50' }}">
                        Home
                    </a>
                    <a href="{{ route('page.show', 'about') }}" class="block px-4 py-3 text-base font-medium rounded-lg touch-manipulation text-gray-700 hover:bg-gray-50">
                        About Us
                    </a>
                    <a href="{{ route('page.show', 'services') }}" class="block px-4 py-3 text-base font-medium rounded-lg touch-manipulation text-gray-700 hover:bg-gray-50">
                        Services
                    </a>
                    <a href="{{ route('page.show', 'training') }}" class="block px-4 py-3 text-base font-medium rounded-lg touch-manipulation text-gray-700 hover:bg-gray-50">
                        Training & Events
                    </a>
                    <a href="{{ route('research.index') }}" class="block px-4 py-3 text-base font-medium rounded-lg touch-manipulation {{ request()->routeIs('research.*') ? 'text-primary-600 bg-primary-50' : 'text-gray-700 hover:bg-gray-50' }}">
                        Resources
                    </a>
                    <a href="{{ route('page.show', 'team') }}" class="block px-4 py-3 text-base font-medium rounded-lg touch-manipulation text-gray-700 hover:bg-gray-50">
                        Team
                    </a>
                    <a href="{{ route('contact.index') }}" class="block px-4 py-3 text-base font-medium rounded-lg touch-manipulation {{ request()->routeIs('contact.*') ? 'text-primary-600 bg-primary-50' : 'text-gray-700 hover:bg-gray-50' }}">
                        Contact Us
                    </a>
                </div>
            </div>
        </nav>
    </header>

    {{-- Breadcrumbs --}}
    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
        <div class="bg-gray-100 border-b border-gray-200">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li>
                            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </a>
                        </li>
                        @foreach($breadcrumbs as $breadcrumb)
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                                @if(isset($breadcrumb['url']))
                                    <a href="{{ $breadcrumb['url'] }}" class="ml-2 text-gray-500 hover:text-gray-700">
                                        {{ $breadcrumb['title'] }}
                                    </a>
                                @else
                                    <span class="ml-2 text-gray-700 font-medium">
                                        {{ $breadcrumb['title'] }}
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 text-gray-300">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- About Section --}}
                <div class="col-span-1 md:col-span-2">
                    <img src="{{ asset('images/logo-white.svg') }}" alt="HEAC Logo" class="h-10 w-auto mb-4">
                    <p class="text-sm text-gray-400 mb-4">
                        HEAC is your trusted partner in Islamic finance solutions, advancing the halal economy globally through comprehensive Shariah-compliant services and expert guidance.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition">
                            <span class="sr-only">LinkedIn</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="text-white font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('page.show', 'about') }}" class="hover:text-white transition">About Us</a></li>
                        <li><a href="{{ route('page.show', 'services') }}" class="hover:text-white transition">Services</a></li>
                        <li><a href="{{ route('page.show', 'training') }}" class="hover:text-white transition">Training & Events</a></li>
                        <li><a href="{{ route('research.index') }}" class="hover:text-white transition">Resources & Publications</a></li>
                        <li><a href="{{ route('page.show', 'team') }}" class="hover:text-white transition">Team & Scholars</a></li>
                        <li><a href="{{ route('contact.index') }}" class="hover:text-white transition">Contact Us</a></li>
                    </ul>
                </div>

                {{-- Contact Info --}}
                <div>
                    <h3 class="text-white font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>123 Education Street, City, Country</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>info@heac.org</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>+1 234 567 8900</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-sm text-center text-gray-400">
                <p>&copy; {{ date('Y') }} HEAC - Halal Economy Accreditation Commission. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

