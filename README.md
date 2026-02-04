# CMS Renderer for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/talhakazmi/cms-renderer.svg)](https://packagist.org/packages/talhakazmi/cms-renderer)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

A **Laravel Blog Renderer** that displays blogs from [Global CMS](https://blogcms.techozon.com) in your Laravel application. Just run one command and your blog is ready!

## ✨ Features

- 🚀 **One Command Setup**: Install with a single Artisan command
- 🎨 **Beautiful Design**: Modern, responsive blog layout
- 🌓 **Light & Dark Mode**: Automatic or manual theme switching
- 🔍 **Built-in Search**: Filter posts instantly
- 📄 **Pagination**: Built-in pagination support
- ⚡ **Caching**: API responses are cached for performance
- 🎯 **Zero Config**: Just add your Organization ID
- 🛡️ **SEO Ready**: Meta tags for social sharing

---

## 📋 Prerequisites

Before installing the package, ensure you have:
- PHP 8.1 or higher
- Composer installed
- An account at [Global CMS](https://blogcms.techozon.com)

### Create a New Laravel Project (Optional)
If you don't have a Laravel project yet, create one:

```bash
composer create-project laravel/laravel my-blog
cd my-blog
```

---

## 🔑 Get Your Organization ID

1. Go to [Global CMS](https://blogcms.techozon.com)
2. Sign up or log in
3. Navigate to **Integration** in the sidebar
4. Copy your **Organization ID**

---

## 🚀 Installation

### Step 1: Install via Composer

```bash
composer require talhakazmi/cms-renderer
```

### Step 2: Run the Install Command

```bash
php artisan cms-renderer:install
```

This will:
- ✅ Publish configuration file
- ✅ Publish view templates
- ✅ Create `BlogController`
- ✅ Add blog routes to `web.php`
- ✅ Update `.env` with CMS settings
- ✅ Create CSS stylesheet

### Step 3: Start Your Server

```bash
php artisan serve
```

Visit `http://localhost:8000/blog` to see your blog! 🎉

---

## ⚙️ Configuration

After installation, you can configure the package in `config/cms-renderer.php`:

```php
return [
    // Your Organization ID from Global CMS
    'organization_id' => env('CMS_ORGANIZATION_ID', ''),

    // API URL
    'api_url' => env('CMS_API_URL', 'https://blogcms.techozon.com/api'),

    // Theme: grid, minimal, magazine, masonry
    'theme' => env('CMS_THEME', 'grid'),

    // Color Mode: auto, light, dark
    'color_mode' => env('CMS_COLOR_MODE', 'dark'),

    // Cache duration in seconds
    'cache_duration' => env('CMS_CACHE_DURATION', 300),

    // Posts per page
    'posts_per_page' => env('CMS_POSTS_PER_PAGE', 9),
];
```

### Environment Variables

Add these to your `.env` file:

```env
CMS_ORGANIZATION_ID=your-organization-id
CMS_API_URL=https://blogcms.techozon.com/api
CMS_THEME=grid
CMS_COLOR_MODE=dark
CMS_CACHE_DURATION=300
CMS_POSTS_PER_PAGE=9
```

---

## 📖 Usage

### Using the Controller (Default)

After installation, the package creates a `BlogController` with two routes:

- `GET /blog` - Blog listing with search and pagination
- `GET /blog/{slug}` - Single blog post

### Using the Facade

You can also use the `CmsRenderer` facade directly:

```php
use TalhaKazmi\CmsRenderer\Facades\CmsRenderer;

// Get all blogs
$blogs = CmsRenderer::getBlogs(page: 1, perPage: 9, search: 'laravel');

// Get a single blog
$blog = CmsRenderer::getBlog('my-blog-slug');

// Clear cache
CmsRenderer::clearCache();
```

### Using Blade Components

You can use the Blade components in your views:

```blade
{{-- Full blog renderer with search and pagination --}}
<x-cms-blog-renderer 
    :organization-id="config('cms-renderer.organization_id')"
    theme="grid"
    color-mode="dark"
/>

{{-- Just the blog list --}}
<x-cms-blog-list :blogs="$blogs" theme="grid" />

{{-- Single blog post --}}
<x-cms-blog-post :blog="$blog" theme="grid" />
```

---

## 🎨 Customization

### Custom Views

Publish the views to customize them:

```bash
php artisan vendor:publish --tag=cms-renderer-views
```

Views will be published to `resources/views/vendor/cms-renderer/`.

### Custom CSS

The CSS file is located at `public/css/cms-renderer.css`. You can modify it directly or override styles in your own CSS.

### CSS Variables

Override these CSS variables to customize the design:

```css
.cms-renderer {
  --cms-bg-primary: #0f172a;
  --cms-bg-secondary: #1e293b;
  --cms-text-primary: #f8fafc;
  --cms-text-secondary: #94a3b8;
  --cms-accent-color: #6366f1;
  --cms-border-color: #334155;
}
```

---

## 🔧 Advanced Usage

### Custom Controller

If you want more control, create your own controller:

```php
<?php

namespace App\Http\Controllers;

use TalhaKazmi\CmsRenderer\Facades\CmsRenderer;

class MyBlogController extends Controller
{
    public function index()
    {
        $data = CmsRenderer::getBlogs(
            page: request('page', 1),
            perPage: 12,
            search: request('search')
        );

        return view('my-blog.index', [
            'blogs' => $data['blogs'],
            'pagination' => $data['pagination'],
        ]);
    }
}
```

### Clear Cache

```php
// Clear all cached blog data
CmsRenderer::clearCache();
```

Or via Artisan:

```bash
php artisan cache:clear
```

---

## 🐛 Troubleshooting

### "No posts found"

- Make sure your blogs are **Published** (not Draft) in Global CMS
- Verify your Organization ID is correct

### SQLite driver error

Change session driver in `.env`:

```env
SESSION_DRIVER=file
```

### Styles not loading

Make sure the CSS is linked in your view:

```html
<link rel="stylesheet" href="{{ asset('css/cms-renderer.css') }}">
```

Or re-run the install command:

```bash
php artisan cms-renderer:install
```

---

## 📝 Publishing Your Blogs

1. Log in to [Global CMS](https://blogcms.techozon.com)
2. Create or edit a blog post
3. Change status from **Draft** to **Published**
4. Save the post
5. The post will appear in your app (after cache expires or is cleared)

---

## 🔒 Security

- ✅ Uses **public, read-only** API endpoints only
- ✅ No API keys exposed to the client
- ✅ Only **published** blog posts are visible
- ✅ Responses are cached to reduce API calls

---

## 📄 License

MIT © [Techozon](https://techozon.com)

---

## 🔗 Links

- [Global CMS Dashboard](https://blogcms.techozon.com)
- [NPM Package](https://www.npmjs.com/package/@talhakazmi/cms-renderer) (for React/Next.js)
- [Packagist](https://packagist.org/packages/talhakazmi/cms-renderer)
- [GitHub Repository](https://github.com/talhakazmi/cms-renderer-laravel)

---

## 💬 Support

Need help? Have questions?

- 📧 Email: support@techozon.com
- 🐛 [Report a Bug](https://github.com/talhakazmi/cms-renderer-laravel/issues)
