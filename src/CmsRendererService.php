<?php

namespace TalhaKazmi\CmsRenderer;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CmsRendererService
{
    protected string $organizationId;
    protected string $apiUrl;
    protected int $cacheDuration;

    public function __construct(?string $organizationId = null, ?string $apiUrl = null)
    {
        $this->organizationId = $organizationId ?? config('cms-renderer.organization_id', '');
        $this->apiUrl = $apiUrl ?? config('cms-renderer.api_url', 'https://blogcms.techozon.com/api');
        $this->cacheDuration = config('cms-renderer.cache_duration', 300);
    }

    /**
     * Get all published blogs
     */
    public function getBlogs(int $page = 1, int $perPage = 9, ?string $search = null): array
    {
        $cacheKey = "cms_blogs_{$this->organizationId}_{$page}_{$perPage}_{$search}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($page, $perPage, $search) {
            $query = [
                'organizationId' => $this->organizationId,
                'page' => $page,
                'limit' => $perPage,
            ];

            if ($search) {
                $query['search'] = $search;
            }

            try {
                $response = Http::withoutVerifying()
                    ->timeout(10)
                    ->get("{$this->apiUrl}/public/blogs", $query);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                // Log error but don't break the app
                \Log::warning('CMS Renderer API Error: ' . $e->getMessage());
            }

            return [
                'blogs' => [],
                'pagination' => [
                    'total' => 0,
                    'page' => $page,
                    'limit' => $perPage,
                    'totalPages' => 0,
                ],
            ];
        });
    }

    /**
     * Get a single blog by slug
     */
    public function getBlog(string $slug): ?array
    {
        $cacheKey = "cms_blog_{$this->organizationId}_{$slug}";

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($slug) {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(10)
                    ->get("{$this->apiUrl}/public/blogs/{$slug}", [
                        'organizationId' => $this->organizationId,
                    ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                \Log::warning('CMS Renderer API Error: ' . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Clear the cache for blogs
     */
    public function clearCache(): void
    {
        Cache::flush();
    }

    /**
     * Format date for display
     */
    public function formatDate(string $date, string $format = 'M d, Y'): string
    {
        return date($format, strtotime($date));
    }

    /**
     * Calculate read time in minutes
     */
    public function calculateReadTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $readTime = ceil($wordCount / 200);
        return max(1, $readTime);
    }

    /**
     * Get excerpt from content
     */
    public function getExcerpt(string $content, int $length = 150): string
    {
        $stripped = strip_tags($content);
        if (strlen($stripped) <= $length) {
            return $stripped;
        }
        return substr($stripped, 0, $length) . '...';
    }
}
