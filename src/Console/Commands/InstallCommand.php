<?php

namespace TalhaKazmi\CmsRenderer\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cms-renderer:install 
                            {--org-id= : Your Organization ID from Global CMS}
                            {--theme=grid : Theme (grid, minimal, magazine, masonry)}
                            {--color-mode=dark : Color mode (auto, light, dark)}';

    /**
     * The console command description.
     */
    protected $description = 'Install CMS Renderer - Set up blog routes, views, and configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('🚀 CMS Renderer Installation');
        $this->info('============================');
        $this->info('');

        // Get organization ID
        $orgId = $this->option('org-id');
        if (!$orgId) {
            $orgId = $this->ask('Enter your Organization ID from Global CMS');
            if (!$orgId) {
                $this->error('Organization ID is required!');
                return 1;
            }
        }

        // Get theme
        $theme = $this->option('theme');
        if (!in_array($theme, ['grid', 'minimal', 'magazine', 'masonry'])) {
            $theme = $this->choice(
                'Choose a theme',
                ['grid', 'minimal', 'magazine', 'masonry'],
                0
            );
        }

        // Get color mode
        $colorMode = $this->option('color-mode');
        if (!in_array($colorMode, ['auto', 'light', 'dark'])) {
            $colorMode = $this->choice(
                'Choose color mode',
                ['auto', 'light', 'dark'],
                2
            );
        }

        $this->info('');
        $this->info('📁 Creating files...');
        $this->info('');

        // 1. Publish config
        $this->call('vendor:publish', [
            '--tag' => 'cms-renderer-config',
            '--force' => true,
        ]);
        $this->info('✔ Published configuration');

        // 2. Publish views
        $this->call('vendor:publish', [
            '--tag' => 'cms-renderer-views',
            '--force' => true,
        ]);
        $this->info('✔ Published views');

        // 3. Create controller
        $this->createController();
        $this->info('✔ Created BlogController');

        // 4. Add routes
        $this->addRoutes();
        $this->info('✔ Added blog routes');

        // 5. Update .env
        $this->updateEnvFile($orgId, $theme, $colorMode);
        $this->info('✔ Updated .env file');

        // 6. Create CSS file
        $this->createCssFile();
        $this->info('✔ Created CSS styles');

        $this->info('');
        $this->info('✅ CMS Renderer installed successfully!');
        $this->info('');
        $this->info('Next steps:');
        $this->info('  1. Run: php artisan serve');
        $this->info('  2. Visit: http://localhost:8000/blog');
        $this->info('');
        $this->info('📖 Documentation: https://www.npmjs.com/package/@talhakazmi/cms-renderer');
        $this->info('');

        return 0;
    }

    /**
     * Create the BlogController
     */
    protected function createController(): void
    {
        $controllerPath = app_path('Http/Controllers/BlogController.php');

        if (File::exists($controllerPath)) {
            if (!$this->confirm('BlogController already exists. Overwrite?', false)) {
                return;
            }
        }

        $stub = <<<'PHP'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TalhaKazmi\CmsRenderer\Facades\CmsRenderer;

class BlogController extends Controller
{
    /**
     * Display the blog list
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $perPage = config('cms-renderer.posts_per_page', 9);

        $data = CmsRenderer::getBlogs($page, $perPage, $search);

        return view('blog.index', [
            'blogs' => $data['blogs'] ?? [],
            'pagination' => $data['pagination'] ?? null,
            'search' => $search,
            'theme' => config('cms-renderer.theme', 'grid'),
            'colorMode' => config('cms-renderer.color_mode', 'dark'),
        ]);
    }

    /**
     * Display a single blog post
     */
    public function show(string $slug)
    {
        $blog = CmsRenderer::getBlog($slug);

        if (!$blog) {
            abort(404, 'Blog post not found');
        }

        return view('blog.show', [
            'blog' => $blog,
            'theme' => config('cms-renderer.theme', 'grid'),
            'colorMode' => config('cms-renderer.color_mode', 'dark'),
        ]);
    }
}
PHP;

        File::ensureDirectoryExists(dirname($controllerPath));
        File::put($controllerPath, $stub);
    }

    /**
     * Add blog routes to web.php
     */
    protected function addRoutes(): void
    {
        $routesPath = base_path('routes/web.php');
        $routeContent = File::get($routesPath);

        // Check if routes already exist
        if (str_contains($routeContent, 'BlogController')) {
            $this->warn('Blog routes already exist in web.php');
            return;
        }

        $routesToAdd = <<<'PHP'

// CMS Renderer Blog Routes
use App\Http\Controllers\BlogController;

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
PHP;

        File::append($routesPath, $routesToAdd);
    }

    /**
     * Update .env file with CMS configuration
     */
    protected function updateEnvFile(string $orgId, string $theme, string $colorMode): void
    {
        $envPath = base_path('.env');
        $envContent = File::exists($envPath) ? File::get($envPath) : '';

        // Check if CMS config already exists
        if (str_contains($envContent, 'CMS_ORGANIZATION_ID')) {
            // Update existing values
            $envContent = preg_replace('/CMS_ORGANIZATION_ID=.*/', "CMS_ORGANIZATION_ID={$orgId}", $envContent);
            $envContent = preg_replace('/CMS_THEME=.*/', "CMS_THEME={$theme}", $envContent);
            $envContent = preg_replace('/CMS_COLOR_MODE=.*/', "CMS_COLOR_MODE={$colorMode}", $envContent);
        } else {
            // Add new config
            $cmsConfig = <<<ENV

# CMS Renderer Configuration
CMS_ORGANIZATION_ID={$orgId}
CMS_API_URL=https://blogcms.techozon.com/api
CMS_THEME={$theme}
CMS_COLOR_MODE={$colorMode}
CMS_CACHE_DURATION=300
CMS_POSTS_PER_PAGE=9
ENV;
            $envContent .= $cmsConfig;
        }

        // Also ensure SESSION_DRIVER=file to avoid SQLite issues
        if (str_contains($envContent, 'SESSION_DRIVER=database')) {
            $envContent = str_replace('SESSION_DRIVER=database', 'SESSION_DRIVER=file', $envContent);
            $this->warn('Changed SESSION_DRIVER from database to file (to avoid SQLite driver issues)');
        }

        File::put($envPath, $envContent);
    }

    /**
     * Create CSS file for blog styling
     */
    protected function createCssFile(): void
    {
        $cssPath = public_path('css/cms-renderer.css');
        File::ensureDirectoryExists(dirname($cssPath));

        $css = $this->getCssContent();
        File::put($cssPath, $css);
    }

    /**
     * Get the CSS content for the blog
     */
    protected function getCssContent(): string
    {
        return <<<'CSS'
/* CMS Renderer Styles */
:root {
  --cms-bg-primary: #0f172a;
  --cms-bg-secondary: #1e293b;
  --cms-bg-tertiary: #334155;
  --cms-text-primary: #f8fafc;
  --cms-text-secondary: #94a3b8;
  --cms-accent-color: #6366f1;
  --cms-accent-hover: #4f46e5;
  --cms-border-color: #334155;
  --cms-font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.cms-light {
  --cms-bg-primary: #ffffff;
  --cms-bg-secondary: #f8fafc;
  --cms-bg-tertiary: #e2e8f0;
  --cms-text-primary: #0f172a;
  --cms-text-secondary: #64748b;
  --cms-border-color: #e2e8f0;
}

.cms-renderer {
  font-family: var(--cms-font-sans);
  background-color: var(--cms-bg-primary);
  color: var(--cms-text-primary);
  min-height: 100vh;
  padding: 2rem 1rem;
}

.cms-container {
  max-width: 1200px;
  margin: 0 auto;
}

.cms-header {
  margin-bottom: 2rem;
  text-align: center;
}

.cms-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.cms-header p {
  color: var(--cms-text-secondary);
}

.cms-search {
  max-width: 400px;
  margin: 0 auto 2rem;
}

.cms-search input {
  width: 100%;
  padding: 0.75rem 1rem;
  border-radius: 0.5rem;
  border: 1px solid var(--cms-border-color);
  background-color: var(--cms-bg-secondary);
  color: var(--cms-text-primary);
  font-size: 1rem;
}

.cms-search input:focus {
  outline: none;
  border-color: var(--cms-accent-color);
}

/* Grid Theme */
.cms-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

/* Card Styles */
.cms-card {
  background-color: var(--cms-bg-secondary);
  border-radius: 1rem;
  overflow: hidden;
  border: 1px solid var(--cms-border-color);
  transition: transform 0.2s, box-shadow 0.2s;
}

.cms-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

.cms-card-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.cms-card-placeholder {
  width: 100%;
  height: 200px;
  background-color: var(--cms-bg-tertiary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
}

.cms-card-content {
  padding: 1.5rem;
}

.cms-card-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.875rem;
  color: var(--cms-text-secondary);
  margin-bottom: 0.75rem;
}

.cms-card-title {
  font-size: 1.25rem;
  font-weight: 600;
  margin-bottom: 0.75rem;
  line-height: 1.4;
}

.cms-card-title a {
  color: var(--cms-text-primary);
  text-decoration: none;
}

.cms-card-title a:hover {
  color: var(--cms-accent-color);
}

.cms-card-excerpt {
  color: var(--cms-text-secondary);
  font-size: 0.9375rem;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.cms-card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.cms-card-author {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.cms-card-author-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.cms-card-author-name {
  font-size: 0.875rem;
  color: var(--cms-text-secondary);
}

.cms-card-link {
  color: var(--cms-accent-color);
  font-weight: 500;
  font-size: 0.875rem;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.cms-card-link:hover {
  color: var(--cms-accent-hover);
}

/* Blog Post Styles */
.cms-post {
  max-width: 800px;
  margin: 0 auto;
}

.cms-post-header {
  margin-bottom: 2rem;
}

.cms-post-back-link {
  color: var(--cms-accent-color);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.cms-post-back-link:hover {
  color: var(--cms-accent-hover);
}

.cms-post-title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.cms-post-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
  align-items: center;
  color: var(--cms-text-secondary);
}

.cms-post-author {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.cms-post-author-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
}

.cms-post-author-name {
  font-weight: 600;
  color: var(--cms-text-primary);
}

.cms-post-author-role {
  font-size: 0.875rem;
}

.cms-post-featured-image {
  width: 100%;
  border-radius: 1rem;
  margin-bottom: 2rem;
}

.cms-post-content {
  font-size: 1.125rem;
  line-height: 1.8;
  color: var(--cms-text-secondary);
}

.cms-post-content h2 {
  font-size: 1.75rem;
  font-weight: 600;
  color: var(--cms-text-primary);
  margin: 2rem 0 1rem;
}

.cms-post-content h3 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--cms-text-primary);
  margin: 1.5rem 0 0.75rem;
}

.cms-post-content p {
  margin-bottom: 1.25rem;
}

.cms-post-content a {
  color: var(--cms-accent-color);
}

.cms-post-content code {
  background-color: var(--cms-bg-tertiary);
  padding: 0.2rem 0.4rem;
  border-radius: 0.25rem;
  font-family: 'Fira Code', monospace;
}

.cms-post-content pre {
  background-color: var(--cms-bg-tertiary);
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
  margin: 1.5rem 0;
}

.cms-post-content ul,
.cms-post-content ol {
  margin: 1rem 0 1rem 1.5rem;
}

.cms-post-content li {
  margin-bottom: 0.5rem;
}

.cms-post-content blockquote {
  border-left: 4px solid var(--cms-accent-color);
  padding-left: 1rem;
  margin: 1.5rem 0;
  font-style: italic;
  color: var(--cms-text-secondary);
}

/* Pagination */
.cms-pagination {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 2rem;
}

.cms-pagination a,
.cms-pagination span {
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  border: 1px solid var(--cms-border-color);
  color: var(--cms-text-primary);
  text-decoration: none;
}

.cms-pagination a:hover {
  background-color: var(--cms-bg-secondary);
}

.cms-pagination .active {
  background-color: var(--cms-accent-color);
  border-color: var(--cms-accent-color);
  color: white;
}

/* Empty State */
.cms-empty {
  text-align: center;
  padding: 4rem 2rem;
  color: var(--cms-text-secondary);
}

.cms-empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .cms-header h1 {
    font-size: 1.75rem;
  }
  
  .cms-post-title {
    font-size: 1.75rem;
  }
  
  .cms-grid {
    grid-template-columns: 1fr;
  }
}
CSS;
    }
}
