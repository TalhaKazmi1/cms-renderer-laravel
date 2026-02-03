<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $blog['title'] }} - {{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="{{ Str::limit(strip_tags($blog['content'] ?? ''), 160) }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $blog['title'] }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($blog['content'] ?? ''), 160) }}">
    @if(!empty($blog['featuredImage']))
        <meta property="og:image" content="{{ $blog['featuredImage'] }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- CMS Renderer Styles -->
    <link rel="stylesheet" href="{{ asset('css/cms-renderer.css') }}">
</head>
<body class="antialiased">
    <div class="cms-renderer {{ ($colorMode ?? 'dark') === 'light' ? 'cms-light' : '' }}">
        <div class="cms-container">
            <article class="cms-post">
                <header class="cms-post-header">
                    <a href="{{ route('blog.index') }}" class="cms-post-back-link">
                        ← Back to all posts
                    </a>

                    <h1 class="cms-post-title">{{ $blog['title'] }}</h1>

                    <div class="cms-post-meta">
                        <div class="cms-post-author">
                            @if(!empty($blog['author']['avatar']))
                                <img 
                                    class="cms-post-author-avatar" 
                                    src="{{ $blog['author']['avatar'] }}" 
                                    alt="{{ $blog['author']['name'] ?? 'Author' }}"
                                >
                            @else
                                <div class="cms-post-author-avatar" style="display: flex; align-items: center; justify-content: center; background: var(--cms-bg-tertiary); width: 48px; height: 48px; border-radius: 50%;">👤</div>
                            @endif
                            <div class="cms-post-author-info">
                                <span class="cms-post-author-name">{{ $blog['author']['name'] ?? 'Unknown Author' }}</span>
                                <span class="cms-post-author-role">Author</span>
                            </div>
                        </div>

                        <span class="cms-post-date">
                            📅 {{ date('M d, Y', strtotime($blog['publishedAt'] ?? $blog['createdAt'])) }}
                        </span>

                        <span class="cms-post-read-time">
                            ⏱️ {{ ceil(str_word_count(strip_tags($blog['content'] ?? '')) / 200) }} min read
                        </span>
                    </div>
                </header>

                @if(!empty($blog['featuredImage']))
                    <img 
                        class="cms-post-featured-image" 
                        src="{{ $blog['featuredImage'] }}" 
                        alt="{{ $blog['title'] }}"
                    >
                @endif

                <div class="cms-post-content">
                    {!! \Illuminate\Support\Str::markdown($blog['content'] ?? '') !!}
                </div>
            </article>
        </div>
    </div>
</body>
</html>
