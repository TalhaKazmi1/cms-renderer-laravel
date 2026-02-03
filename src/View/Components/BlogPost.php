<?php

namespace TalhaKazmi\CmsRenderer\View\Components;

use Illuminate\View\Component;

class BlogPost extends Component
{
    public array $blog;
    public string $theme;

    public function __construct(
        array $blog,
        string $theme = 'grid'
    ) {
        $this->blog = $blog;
        $this->theme = $theme;
    }

    public function render()
    {
        return view('cms-renderer::components.blog-post');
    }
}
