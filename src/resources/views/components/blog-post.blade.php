{{-- Blog Post Component --}}
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
