<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Blog - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- CMS Renderer Styles -->
    <link rel="stylesheet" href="{{ asset('css/cms-renderer.css') }}">
</head>
<body class="antialiased">
    <div class="cms-renderer {{ ($colorMode ?? 'dark') === 'light' ? 'cms-light' : '' }}">
        <div class="cms-container">
            <header class="cms-header">
                <h1>Blog</h1>
                <p>Latest articles and updates</p>
            </header>

            <div class="cms-search">
                <form action="{{ route('blog.index') }}" method="GET">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search articles..." 
                        value="{{ $search ?? '' }}"
                    >
                </form>
            </div>

            @if(count($blogs) > 0)
                <div class="cms-grid">
                    @foreach($blogs as $blog)
                        <article class="cms-card">
                            <div class="cms-card-image-wrapper">
                                @if(!empty($blog['featuredImage']))
                                    <img 
                                        class="cms-card-image" 
                                        src="{{ $blog['featuredImage'] }}" 
                                        alt="{{ $blog['title'] }}"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="cms-card-placeholder">📄</div>
                                @endif
                            </div>
                            
                            <div class="cms-card-content">
                                <div class="cms-card-meta">
                                    <span>📅 {{ date('M d, Y', strtotime($blog['publishedAt'] ?? $blog['createdAt'])) }}</span>
                                    <span>⏱️ {{ ceil(str_word_count(strip_tags($blog['content'] ?? '')) / 200) }} min read</span>
                                </div>

                                <h2 class="cms-card-title">
                                    <a href="{{ route('blog.show', $blog['slug']) }}">
                                        {{ $blog['title'] }}
                                    </a>
                                </h2>

                                @if(!empty($blog['excerpt']))
                                    <p class="cms-card-excerpt">{{ Str::limit($blog['excerpt'], 150) }}</p>
                                @endif

                                <div class="cms-card-footer">
                                    <div class="cms-card-author">
                                        @if(!empty($blog['author']['avatar']))
                                            <img 
                                                class="cms-card-author-avatar" 
                                                src="{{ $blog['author']['avatar'] }}" 
                                                alt="{{ $blog['author']['name'] ?? 'Author' }}"
                                            >
                                        @else
                                            <div class="cms-card-author-avatar" style="display: flex; align-items: center; justify-content: center; background: var(--cms-bg-tertiary);">👤</div>
                                        @endif
                                        <span class="cms-card-author-name">{{ $blog['author']['name'] ?? 'Unknown Author' }}</span>
                                    </div>

                                    <a class="cms-card-link" href="{{ route('blog.show', $blog['slug']) }}">
                                        Read more →
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($pagination && $pagination['totalPages'] > 1)
                    <div class="cms-pagination">
                        @if($pagination['page'] > 1)
                            <a href="{{ route('blog.index', ['page' => $pagination['page'] - 1, 'search' => $search]) }}">
                                ← Previous
                            </a>
                        @endif

                        @for($i = 1; $i <= min($pagination['totalPages'], 5); $i++)
                            @if($i == $pagination['page'])
                                <span class="active">{{ $i }}</span>
                            @else
                                <a href="{{ route('blog.index', ['page' => $i, 'search' => $search]) }}">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        @if($pagination['page'] < $pagination['totalPages'])
                            <a href="{{ route('blog.index', ['page' => $pagination['page'] + 1, 'search' => $search]) }}">
                                Next →
                            </a>
                        @endif
                    </div>
                @endif
            @else
                <div class="cms-empty">
                    <div class="cms-empty-icon">📭</div>
                    <h3>No posts found</h3>
                    <p>Check back later for new content!</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
