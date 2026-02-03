{{-- Blog Renderer Component --}}
<div class="cms-renderer {{ $colorMode === 'light' ? 'cms-light' : '' }}">
    <div class="cms-container">
        @if($showHeader)
            <header class="cms-header">
                <h1>Blog</h1>
                <p>Latest articles and updates</p>
            </header>
        @endif

        @if($showSearch)
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
        @endif

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

                    @for($i = 1; $i <= $pagination['totalPages']; $i++)
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
