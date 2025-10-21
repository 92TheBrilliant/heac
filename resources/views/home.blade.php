@extends('layouts.app')

@push('structured-data')
    <x-structured-data :data="$organizationData ?? []" />
@endpush

@section('content')
{{-- Hero Section --}}
<section class="relative overflow-hidden text-white">
    <div class="absolute inset-0">
        <div class="hero-background"></div>
        <div class="absolute inset-0 bg-slate-900/80 mix-blend-multiply"></div>
        <div class="absolute inset-0 hero-overlay-pattern"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary-500/30 blur-3xl rounded-full"></div>
        <div class="absolute -bottom-32 -left-10 w-80 h-80 bg-blue-400/20 blur-[140px] rounded-full"></div>
    </div>

    <div class="relative">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="grid gap-12 lg:grid-cols-[1.1fr_minmax(0,0.9fr)] items-center">
                <div class="space-y-8">
                    <span class="highlight-chip" style="--chip-bg: rgba(255, 255, 255, 0.14); --chip-color: #ffffff;">
                        Global Shariah Leadership
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight">
                        {{ $heroTitle ?? 'Your Trusted Partner in Islamic Finance Solutions' }}
                    </h1>
                    <p class="text-lg md:text-xl text-blue-100 max-w-2xl">
                        {{ $heroSubtitle ?? 'Advancing the halal economy globally through comprehensive Shariah-compliant solutions and expert guidance' }}
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('contact.index') }}" class="btn-primary btn-lg shadow-soft">
                            Book a Consultation
                        </a>
                        <a href="{{ route('page.show', 'services') }}" class="btn-outline btn-lg border-white text-white hover:bg-white hover:text-primary-600">
                            Explore Services
                        </a>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div class="flex items-start gap-4">
                            <div class="icon-circle bg-white/10 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold">Shariah Governance Experts</p>
                                <p class="text-sm text-blue-100/80">Accredited scholars guiding Islamic finance innovation across markets.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="icon-circle bg-white/10 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v6a9 9 0 0018 0V7M3 7a9 9 0 0118 0M3 7a9 9 0 0118 0" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold">Global Multi-lingual Advisors</p>
                                <p class="text-sm text-blue-100/80">Serving banking, fintech, and halal enterprises across three continents.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="glass border border-white/10 rounded-3xl p-8 shadow-strong space-y-6">
                    <div class="flex items-center justify-between gap-6">
                        <div>
                            <p class="text-sm uppercase tracking-[0.3em] text-blue-100/70">Comprehensive Support</p>
                            <h3 class="text-2xl font-semibold">Tailored Shariah Solutions</h3>
                        </div>
                        <div class="badge" style="--chip-bg: rgba(255,255,255,0.15); --chip-color: #ffffff;">
                            24/7</div>
                    </div>
                    <p class="text-blue-100/80 leading-relaxed">
                        From product structuring to audits and certification, HEAC delivers end-to-end engagement anchored in AAOIFI and international best practices.
                    </p>
                    <ul class="space-y-4 text-sm text-blue-100/80">
                        <li class="flex items-start gap-3">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/15 text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            Strategic advisory boards for Islamic banks, fintech, and Takaful.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/15 text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            Certified scholars delivering timely fatwas and halal certifications.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/15 text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            Impact-focused training programs for executives and compliance teams.
                        </li>
                    </ul>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex-1">
                            <div class="text-3xl font-semibold">100+</div>
                            <p class="text-xs uppercase tracking-wide text-blue-100/70">Institutions guided worldwide</p>
                        </div>
                        <a href="{{ route('contact.index') }}" class="btn btn-outline border-white text-white hover:bg-white hover:text-primary-600">
                            Speak to an Advisor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($statistics) && count($statistics) > 0)
    <div class="relative pb-6">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-3 lg:grid-cols-{{ min(count($statistics), 4) }} -mt-10 md:-mt-14">
                @foreach($statistics as $stat)
                <div class="glass border border-white/30 rounded-2xl p-6 text-center shadow-soft backdrop-blur-md">
                    <div class="text-3xl md:text-4xl font-bold text-white mb-2">
                        {{ $stat['value'] }}
                    </div>
                    <div class="text-sm uppercase tracking-wide text-blue-100/80">
                        {{ $stat['label'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</section>

{{-- Trusted By Section --}}
<section class="py-12 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-primary-600">Trusted by leading institutions</p>
                <h2 class="mt-3 text-2xl font-semibold text-gray-900">
                    Strategic partner for banks, fintech innovators, and halal enterprises across the globe.
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-4 lg:justify-end">
                @foreach(['Al Noor Bank', 'Unity Islamic', 'Global Takaful', 'Sukuk Holdings', 'Halal Ventures'] as $client)
                <span class="client-logo">{{ $client }}</span>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Why Choose HEAC Section --}}
<section class="py-16 bg-slate-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="section-heading text-center">
            <span class="highlight-chip">Why Choose HEAC</span>
            <h2 class="mt-4 text-3xl md:text-4xl font-bold text-gray-900">
                Confidence, compliance, and growth for your Islamic finance vision
            </h2>
            <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                We bring a multidisciplinary team of certified scholars and industry experts to every engagement, ensuring excellence from ideation to certification.
            </p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="advantage-card">
                <div class="icon-circle bg-blue-100 text-primary-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3>Recognized Shariah Authorities</h3>
                <p>AAOIFI-aligned scholars with decades of experience across banking, Takaful, and fintech.</p>
            </div>

            <div class="advantage-card">
                <div class="icon-circle bg-emerald-100 text-emerald-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3>End-to-End Service Portfolio</h3>
                <p>From advisory and structuring to certification and training, we manage the entire compliance lifecycle.</p>
            </div>

            <div class="advantage-card">
                <div class="icon-circle bg-amber-100 text-amber-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.423 3.423 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <h3>Licensed & Regulated</h3>
                <p>Registered with key regulators and compliant with international Shariah governance standards.</p>
            </div>

            <div class="advantage-card">
                <div class="icon-circle bg-purple-100 text-purple-600">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3>Worldwide Reach</h3>
                <p>Advisors fluent in Arabic, English, Malay, and Urdu supporting clients across 20+ markets.</p>
            </div>
        </div>
    </div>
</section>

{{-- Our Approach Section --}}
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="section-heading text-center">
            <span class="highlight-chip">Our Methodology</span>
            <h2 class="mt-4 text-3xl md:text-4xl font-bold text-gray-900">
                A collaborative framework that delivers measurable impact
            </h2>
            <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                Every engagement follows a disciplined pathway built around discovery, Shariah research, implementation, and continuous improvement.
            </p>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            @php
                $approachSteps = [
                    ['title' => 'Discover & Diagnose', 'description' => 'Stakeholder workshops to understand objectives, regulatory context, and operational realities.', 'icon' => 'M13 7H7m6 10H7m10-3.586V17a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h8a2 2 0 012 2v2.414a2 2 0 01-.586 1.414l-1 1a2 2 0 010 2.828l1 1A2 2 0 0117 13.414z'],
                    ['title' => 'Research & Structuring', 'description' => 'Develop robust Shariah-compliant models with peer benchmarking and scenario analysis.', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1'],
                    ['title' => 'Review & Certification', 'description' => 'Iterative scholar reviews culminating in formal fatwa issuance and certification packs.', 'icon' => 'M9 12l2 2 4-4m0 0l-4-4m4 4H7m6 8H7'],
                    ['title' => 'Train & Support', 'description' => 'Executive coaching, compliance training, and digital resources that sustain long-term excellence.', 'icon' => 'M12 6v6l4 2'],
                ];
            @endphp
            @foreach($approachSteps as $step)
            <div class="approach-card">
                <div class="icon-circle icon-circle-outline">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}" />
                    </svg>
                </div>
                <h3>{{ $step['title'] }}</h3>
                <p>{{ $step['description'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- Featured Services Section --}}
<section id="services" class="py-20 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="section-heading text-center">
            <span class="highlight-chip">Our Expertise</span>
            <h2 class="mt-4 text-3xl md:text-4xl font-bold text-gray-900">
                Comprehensive Shariah services designed for modern institutions
            </h2>
            <p class="mt-4 text-lg text-gray-600 max-w-3xl mx-auto">
                HEAC delivers a full spectrum of advisory, certification, structuring, and training capabilities to accelerate your halal economy ambitions.
            </p>
        </div>

        @php
            $servicesOverview = [
                [
                    'title' => 'Shariah Advisory & Consultancy',
                    'description' => 'Strategic guidance for Islamic banks, fintech innovators, asset managers, and halal enterprises.',
                    'highlights' => ['Product structuring & innovation', 'Regulatory engagement & governance', 'Treasury, capital market, and fintech advisory'],
                    'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    'cta' => ['label' => 'Discuss Advisory', 'route' => route('page.show', 'services')],
                ],
                [
                    'title' => 'Shariah Audit & Compliance',
                    'description' => 'Independent internal and external reviews that strengthen governance and investor confidence.',
                    'highlights' => ['Risk-based internal Shariah audit', 'Policy & SOP development', 'AAOIFI and IFSB compliance reports'],
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                    'cta' => ['label' => 'Book a Compliance Review', 'route' => route('page.show', 'services')],
                ],
                [
                    'title' => 'Sukuk & Islamic Structuring',
                    'description' => 'Tailored structures for Sukuk, Islamic funds, Takaful models, and emerging asset classes.',
                    'highlights' => ['Sustainable & ESG Sukuk design', 'Digital assets & fintech compliance', 'Islamic fund launch support'],
                    'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'cta' => ['label' => 'Plan Your Structure', 'route' => route('page.show', 'services')],
                ],
                [
                    'title' => 'Halal Certification & Fatwa Issuance',
                    'description' => 'Formal opinions, certification, and halal business audits delivered by renowned scholars.',
                    'highlights' => ['Consumer products & halal supply chains', 'Fintech & digital platform certification', 'Responsive fatwa and advisory desk'],
                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    'cta' => ['label' => 'Request Certification', 'route' => route('page.show', 'services')],
                ],
                [
                    'title' => 'Training & Executive Education',
                    'description' => 'Immersive learning experiences for boards, executives, and Shariah compliance teams.',
                    'highlights' => ['Certified Shariah Auditor programs', 'Fintech & Shariah innovation labs', 'Customized corporate academies'],
                    'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                    'cta' => ['label' => 'Explore Trainings', 'route' => route('page.show', 'training')],
                ],
                [
                    'title' => 'Zakat, Waqf & Ethical Finance',
                    'description' => 'Holistic wealth purification, philanthropy, and ethical investment screening services.',
                    'highlights' => ['Corporate & personal Zakat strategy', 'Waqf governance frameworks', 'Shariah portfolio screening & reporting'],
                    'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                    'cta' => ['label' => 'Schedule a Consultation', 'route' => route('page.show', 'services')],
                ],
            ];
        @endphp

        <div class="mt-12 grid gap-8 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($servicesOverview as $service)
            <article class="service-card">
                <div class="service-icon">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $service['icon'] }}" />
                    </svg>
                </div>
                <h3>{{ $service['title'] }}</h3>
                <p>{{ $service['description'] }}</p>
                <ul class="service-list">
                    @foreach($service['highlights'] as $item)
                    <li>
                        <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ $service['cta']['route'] }}" class="service-cta">
                    {{ $service['cta']['label'] }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </article>
            @endforeach
        </div>

        <div class="mt-16 grid gap-6 lg:grid-cols-3">
            <div class="info-banner">
                <h3>Industry-certified scholars</h3>
                <p>Our team includes AAOIFI, ISRA, and SECP-recognized scholars providing peer-reviewed opinions.</p>
            </div>
            <div class="info-banner">
                <h3>Global delivery model</h3>
                <p>Engagement hubs in Karachi, Kuala Lumpur, and London ensure rapid deployment across time zones.</p>
            </div>
            <div class="info-banner">
                <h3>Outcome-focused reporting</h3>
                <p>Each service culminates with actionable dashboards, audit trails, and implementation roadmaps.</p>
            </div>
        </div>
    </div>
</section>


{{-- Testimonials Section --}}
<section class="py-20 bg-slate-900 text-white" x-data="testimonialSlider()" x-init="init()" aria-labelledby="testimonial-heading">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[1.1fr_minmax(0,0.9fr)] items-start">
            <div>
                <span class="highlight-chip" style="--chip-bg: rgba(255, 255, 255, 0.12); --chip-color: #ffffff;">Client Stories</span>
                <h2 id="testimonial-heading" class="mt-4 text-3xl md:text-4xl font-bold">Partners worldwide trust HEAC to deliver measurable impact</h2>
                <p class="mt-4 text-blue-100/80 max-w-2xl">Hear from the institutions who rely on our scholars to launch innovative Sukuk, strengthen Shariah governance, and upskill their teams.</p>
                <div class="mt-10 grid gap-6 sm:grid-cols-2">
                    <div class="testimonial-stat">
                        <span>98%</span>
                        <p>Client satisfaction score across advisory and audit engagements.</p>
                    </div>
                    <div class="testimonial-stat">
                        <span>45+</span>
                        <p>Successful Sukuk, fund, and fintech structures certified in the last 24 months.</p>
                    </div>
                </div>
            </div>

            <div class="testimonial-wrapper" role="region" aria-live="polite" aria-atomic="true" @mouseenter="stop()" @mouseleave="start()" @focusin="stop()" @focusout="start()">
                <template x-for="(testimonial, index) in testimonials" :key="index">
                    <article x-show="active === index" x-transition.opacity.scale class="testimonial-card">
                        <div class="flex items-center gap-3 text-sm font-semibold uppercase tracking-[0.25em] text-primary-300">
                            <span>Testimonial</span>
                            <span class="w-10 h-px bg-primary-400/60"></span>
                            <span x-text="testimonial.sector"></span>
                        </div>
                        <blockquote class="mt-6 text-lg leading-relaxed text-white/90" x-text="testimonial.quote"></blockquote>
                        <div class="mt-8 flex items-center justify-between gap-4">
                            <div>
                                <p class="text-base font-semibold text-white" x-text="testimonial.name"></p>
                                <p class="text-sm text-blue-100/80" x-text="testimonial.role"></p>
                            </div>
                            <span class="client-logo" style="--client-bg: rgba(255, 255, 255, 0.12); --client-color: #ffffff;" x-text="testimonial.client"></span>
                        </div>
                    </article>
                </template>

                <div class="testimonial-nav">
                    <div class="flex items-center gap-3">
                        <button type="button" class="nav-button" @click="prev()" aria-label="Previous testimonial">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button type="button" class="nav-button" @click="next()" aria-label="Next testimonial">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-2">
                        <template x-for="(testimonial, index) in testimonials" :key="`dot-${index}`">
                            <button
                                type="button"
                                class="testimonial-dot"
                                :class="{ 'is-active': active === index }"
                                @click="goTo(index)"
                                aria-label="Go to testimonial"
                                :aria-current="active === index ? 'true' : null"
                            ></button>
                        </template>
                    </div>
                </div>
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
<section class="relative py-20 text-white overflow-hidden">
    <div class="absolute inset-0 -z-10">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-700"></div>
        <div class="absolute inset-0 hero-overlay-pattern opacity-40"></div>
        <div class="absolute -top-24 -left-16 w-64 h-64 bg-sky-400/30 blur-3xl rounded-full"></div>
        <div class="absolute -bottom-32 right-0 w-80 h-80 bg-indigo-500/30 blur-3xl rounded-full"></div>
    </div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[1.1fr_auto] items-center">
            <div>
                <span class="highlight-chip" style="--chip-bg: rgba(255, 255, 255, 0.14); --chip-color: #ffffff;">Partner with HEAC</span>
                <h2 class="mt-4 text-3xl md:text-4xl font-bold leading-tight">
                    Let’s build your next Shariah-compliant success story
                </h2>
                <p class="mt-5 text-blue-100/90 max-w-2xl">
                    Whether you need a rapid compliance review, a new Sukuk structure, or executive training, our scholars and analysts respond within one business day.
                </p>
                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-sky-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="text-sm text-blue-100/90">Dedicated engagement managers for banks, fintech, and halal enterprises.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-sky-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="text-sm text-blue-100/90">Transparent deliverables with actionable playbooks and certification roadmaps.</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('contact.index') }}" class="btn-primary btn-lg shadow-soft">
                    Request a Strategy Call
                </a>
                <a href="{{ route('page.show', 'services') }}" class="btn btn-outline border-white text-white hover:bg-white hover:text-indigo-700">
                    Explore Services
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
