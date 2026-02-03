<?php

namespace TalhaKazmi\CmsRenderer\View\Components;

use Illuminate\View\Component;

class BlogList extends Component
{
    public array $blogs;
    public string $theme;

    public function __construct(
        array $blogs = [],
        string $theme = 'grid'
    ) {
        $this->blogs = $blogs;
        $this->theme = $theme;
    }

    public function render()
    {
        return view('cms-renderer::components.blog-list');
    }
}
