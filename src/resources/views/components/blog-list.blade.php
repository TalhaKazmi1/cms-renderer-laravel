{{-- Blog List Component --}}
<div class="cms-grid">
    @forelse($blogs as $blog)
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
                        <span class="cms-card-author-name">{{ $blog['author']['name'] ?? 'Unknown' }}</span>
                    </div>
                    <a class="cms-card-link" href="{{ route('blog.show', $blog['slug']) }}">
                        Read more →
                    </a>
                </div>
            </div>
        </article>
    @empty
        <div class="cms-empty" style="grid-column: 1/-1;">
            <div class="cms-empty-icon">📭</div>
            <h3>No posts found</h3>
        </div>
    @endforelse
</div>
