<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
    $siteName = 'Vidhya Studio';
    $seo = [
        'title' => filled($title ?? null) ? $title.' - '.$siteName : $siteName,
        'description' => 'Vidhya Studio is a creative AI studio delivering cinematic perspective, AI speed, and measurable impact for brands, agencies, and performance-led marketers.',
        'type' => 'website',
    ];

    if (request()->routeIs('home')) {
        $seo['title'] = 'Vidhya Studio - Cinematic Perspective, AI Speed, Measurable Impact';
        $seo['description'] = 'A creative AI studio backed by cinematic production expertise, building premium AI advertising, content systems, micro dramas, and scalable visual campaigns.';
    } elseif (request()->routeIs('about')) {
        $seo['title'] = 'About Vidhya Studio - Filmmaking DNA Meets AI Craft';
        $seo['description'] = 'Learn how Vidhya Studio blends two decades of cinematic excellence from Benetone Films with structured AI workflows and human creative direction.';
    } elseif (request()->routeIs('services')) {
        $seo['title'] = 'AI Creative Services - Vidhya Studio';
        $seo['description'] = 'Explore AI POCs, AI advertising, AI post production, AI models, marketing content, micro drama, workshops, and strategic consulting from Vidhya Studio.';
    } elseif (request()->routeIs('ai-director')) {
        $seo['title'] = 'AI Director - Sunil Thomas - Vidhya Studio';
        $seo['description'] = 'Meet Sunil Thomas, Commercial Director & AI Filmmaker at Vidhya Studio. Combining traditional cinematic direction with advanced AI workflows.';
    } elseif (request()->routeIs('faq')) {
        $seo['title'] = 'Frequently Asked Questions - Vidhya Studio';
        $seo['description'] = 'Find answers to common questions about Vidhya Studio, cinematic AI video workflows, services, creative process, and production timelines.';
    } elseif (request()->routeIs('portfolio')) {
        $seo['title'] = 'Cinematic AI Portfolio - Vidhya Studio';
        $seo['description'] = 'Browse our creative portfolio of director-led AI video works, advertising campaigns, cinematic trailers, and commercial productions.';
    } elseif (request()->routeIs('contact')) {
        $seo['title'] = 'Contact Vidhya Studio - Start a Project';
        $seo['description'] = 'Start a conversation with Vidhya Studio about AI creative strategy, premium visual content, advertising campaigns, and scalable production workflows.';
    }

    $isFrontendPage = request()->routeIs('home', 'about', 'services', 'ai-director', 'portfolio', 'faq', 'contact');
    $canonical = url()->current();
    $ogImage = asset('images/vidhya-studio-logo.png');
    $organizationSchema = json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteName,
        'url' => url('/'),
        'logo' => asset('images/vidhya-studio-logo.png'),
        'description' => $seo['description'],
        'sameAs' => [],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@if ($isFrontendPage && !empty(config('services.google.analytics_id')))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google.analytics_id') }}');
    </script>
@endif

<title>
    {{ $seo['title'] }}
</title>

<meta name="description" content="{{ $seo['description'] }}">
<meta name="robots" content="{{ $isFrontendPage ? 'index, follow' : 'noindex, nofollow' }}">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:type" content="{{ $seo['type'] }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:locale" content="en_US">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo['title'] }}">
<meta name="twitter:description" content="{{ $seo['description'] }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<link rel="icon" href="/favicon.ico?v=2" sizes="any">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" href="/fonts/vidhya/poppins-regular.ttf" as="font" type="font/ttf" crossorigin>
<link rel="preload" href="/fonts/vidhya/poppins-bold.ttf" as="font" type="font/ttf" crossorigin>
<link rel="preload" href="/fonts/vidhya/lemon-milk-bold.otf" as="font" type="font/otf" crossorigin>

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

@if ($isFrontendPage)
    <script type="application/ld+json">
        {!! $organizationSchema !!}
    </script>
@endif
