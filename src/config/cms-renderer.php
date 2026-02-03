<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CMS Renderer Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Global CMS integration settings here.
    | Get your Organization ID from https://blogcms.techozon.com
    |
    */

    // Your Organization ID from Global CMS dashboard
    'organization_id' => env('CMS_ORGANIZATION_ID', ''),

    // API URL (default: Global CMS API)
    'api_url' => env('CMS_API_URL', 'https://blogcms.techozon.com/api'),

    // Theme: grid, minimal, magazine, masonry
    'theme' => env('CMS_THEME', 'grid'),

    // Color Mode: auto, light, dark
    'color_mode' => env('CMS_COLOR_MODE', 'dark'),

    // Cache duration in seconds (default: 5 minutes)
    'cache_duration' => env('CMS_CACHE_DURATION', 300),

    // Posts per page
    'posts_per_page' => env('CMS_POSTS_PER_PAGE', 9),

    // Base path for blog routes
    'base_path' => env('CMS_BASE_PATH', '/blog'),

    // Show header with organization name
    'show_header' => env('CMS_SHOW_HEADER', true),

    // Show search input
    'show_search' => env('CMS_SHOW_SEARCH', true),
];
