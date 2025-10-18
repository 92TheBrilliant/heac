@extends('layouts.app')

@push('structured-data')
    <x-structured-data :data="$organizationData ?? []" />
@endpush

@section('content')
{{-- Hero Section --}}
<section class="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
        <div class="max-w-4xl">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                {{ $heroTitle ?? 'Your Trusted Partner in Islamic Finance Solutions' }}
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-blue-100">
                {{ $heroSubtitle ?? 'Advancing the halal economy globally through comprehensive Shariah-compliant solutions and expert guidance' }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('contact.index') }}" class="btn-primary btn-lg">
                    Book a Consultation
                </a>
                <a href="{{ route('page.show', 'services') }}" class="btn-outline btn-lg border-white text-white hover:bg-white hover:text-primary-600">
                    Our Services
                </a>
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="rgb(249, 250, 251)"/>
        </svg>
    </div>
</section>

{{-- Why Choose HEAC Section --}}
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Why Choose HEAC
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Your partner in achieving Shariah compliance and ethical business practices
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Global Shariah Expertise</h3>
                <p class="text-gray-600">Team of recognized scholars with worldwide experience</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Comprehensive Services</h3>
                <p class="text-gray-600">Full spectrum of Islamic finance and halal advisory</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Licensed & Certified</h3>
                <p class="text-gray-600">Industry-certified with regulatory compliance</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Worldwide Reach</h3>
                <p class="text-gray-600">Multilingual advisors serving clients globally</p>
            </div>
        </div>
    </div>
</section>

{{-- Statistics Section --}}
@if(isset($statistics) && count($statistics) > 0)
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($statistics as $stat)
            <div class="text-center">
                <div class="text-4xl md:text-5xl font-bold text-blue-600 mb-2">
                    {{ $stat['value'] }}
                </div>
                <div class="text-gray-600 text-lg">
                    {{ $stat['label'] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Featured Services Section --}}
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Our Core Services
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Comprehensive Shariah-compliant solutions tailored to your needs
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Shariah Advisory</h3>
                <p class="text-gray-600 mb-4">Comprehensive Islamic finance advisory services tailored to your business needs</p>
                <a href="{{ route('page.show', 'services') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Learn More →</a>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Audit & Compliance</h3>
                <p class="text-gray-600 mb-4">Expert audits to ensure faith-based integrity and regulatory compliance</p>
                <a href="{{ route('page.show', 'services') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Learn More →</a>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Sukuk Structuring</h3>
                <p class="text-gray-600 mb-4">Custom Shariah structures for Islamic bonds and financial instruments</p>
                <a href="{{ route('page.show', 'services') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Learn More →</a>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Halal Certification</h3>
                <p class="text-gray-600 mb-4">Comprehensive halal certification for products and business operations</p>
                <a href="{{ route('page.show', 'services') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Learn More →</a>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Training & Education</h3>
                <p class="text-gray-600 mb-4">Custom training programs and workshops on Islamic finance principles</p>
                <a href="{{ route('page.show', 'training') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Learn More →</a>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Zakat Advisory</h3>
                <p class="text-gray-600 mb-4">Expert guidance on Zakat calculation and management for individuals and organizations</p>
                <a href="{{ route('page.show', 'services') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Learn More →</a>
            </div>
        </div>
    </div>
</section>

{{-- Featured Research Section --}}
@if(isset($featuredResearch) && $featuredResearch->count() > 0)
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Latest Publications & Insights
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Explore our latest research and thought leadership on Islamic finance
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredResearch as $research)
            <article class="card-hover hover-lift transition-smooth">
                @if($research->thumbnail)
                <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                    <img src="{{ Storage::url($research->thumbnail) }}" alt="{{ $research->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
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
                        @if($research->categories->count() > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $research->categories->first()->name }}
                        </span>
                        @endif
                        <span class="text-sm text-gray-500">
                            {{ $research->publication_date->format('M d, Y') }}
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition line-clamp-2">
                        <a href="{{ route('research.show', $research->slug) }}">
                            {{ $research->title }}
                        </a>
                    </h3>
                    
                    <p class="text-gray-600 mb-4 line-clamp-3">
                        {{ $research->abstract }}
                    </p>
                    
                    <div class="flex items-center justify-between">
                        <a href="{{ route('research.show', $research->slug) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm inline-flex items-center">
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
                                {{ $research->views_count }}
                            </span>
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                {{ $research->downloads_count }}
                            </span>
                        </div>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('research.index') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition">
                View All Research
            </a>
        </div>
    </div>
</section>
@endif

{{-- Latest News/Pages Section --}}
@if(isset($latestPages) && $latestPages->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Latest Updates
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Stay informed with our latest news and announcements
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            @foreach($latestPages as $page)
            <article class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-6">
                <div class="text-sm text-gray-500 mb-2">
                    {{ $page->published_at->format('M d, Y') }}
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3 hover:text-blue-600 transition">
                    <a href="{{ route('page.show', $page->slug) }}">
                        {{ $page->title }}
                    </a>
                </h3>
                @if($page->excerpt)
                <p class="text-gray-600 mb-4 line-clamp-2">
                    {{ $page->excerpt }}
                </p>
                @endif
                <a href="{{ route('page.show', $page->slug) }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm inline-flex items-center">
                    Read More
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Call to Action Section --}}
<section class="py-16 bg-blue-600 text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                Ready to Get Started?
            </h2>
            <p class="text-xl text-blue-100 mb-8">
                Contact us today to learn more about our accreditation services and how we can help your institution achieve excellence
            </p>
            <a href="{{ route('contact.index') }}" class="inline-flex items-center justify-center px-8 py-3 border-2 border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-blue-600 transition">
                Contact Us
            </a>
        </div>
    </div>
</section>
@endsection
