<?php

namespace TalhaKazmi\CmsRenderer\View\Components;

use Illuminate\View\Component;
use TalhaKazmi\CmsRenderer\CmsRendererService;

class BlogRenderer extends Component
{
    public array $blogs;
    public ?array $pagination;
    public string $theme;
    public string $colorMode;
    public bool $showHeader;
    public bool $showSearch;
    public ?string $search;

    public function __construct(
        ?string $organizationId = null,
        ?string $apiUrl = null,
        string $theme = 'grid',
        string $colorMode = 'dark',
        bool $showHeader = true,
        bool $showSearch = true,
        int $page = 1,
        int $perPage = 9,
        ?string $search = null
    ) {
        $service = new CmsRendererService($organizationId, $apiUrl);
        $data = $service->getBlogs($page, $perPage, $search);

        $this->blogs = $data['blogs'] ?? [];
        $this->pagination = $data['pagination'] ?? null;
        $this->theme = $theme;
        $this->colorMode = $colorMode;
        $this->showHeader = $showHeader;
        $this->showSearch = $showSearch;
        $this->search = $search;
    }

    public function render()
    {
        return view('cms-renderer::components.blog-renderer');
    }
}
