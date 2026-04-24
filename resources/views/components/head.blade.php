@props(['title' => null, 'seoKey' => null])
<head>
@php
    $seo = $seoKey ? \Illuminate\Support\Facades\Cache::remember("seo_arr.{$seoKey}", now()->addDays(7), function () use ($seoKey) {
        $record = \App\Models\Seo::where('key', $seoKey)->first();
        return $record ? $record->toArray() : null;
    }) : null;

    $finalTitle = $title ?? ($seo['title'] ?? 'QuickJHA Create JHA, AHA & JSA in Minutes OSHA Compliant');
    $description = $seo['description'] ?? 'Quickly create professional JHA (Job Hazard Analysis), AHA (Activity Hazard Analysis), and JSA (Job Safety Analysis) documents. OSHA compliant and mobile ready.';
    $keywords = $seo['keywords'] ?? 'JHA, AHA, JSA, Safety Documents, OSHA Compliant, Job Hazard Analysis, Activity Hazard Analysis';
    $ogImage = !empty($seo['og_image']) ? $seo['og_image'] : asset('og-image.png');
@endphp

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $finalTitle }}</title>

    <!-- Meta Tags for SEO -->
    <meta name="description" content="{{ $description }}">
    <meta name="keywords" content="{{ $keywords }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $finalTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ $ogImage }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $finalTitle }}">
    <meta property="twitter:description" content="{{ $description }}">
    <meta property="twitter:image" content="{{ $ogImage }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Fonts - Standardized for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Montserrat:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
    {{ $slot }}
</head>