# Publishing CMS Renderer to Packagist

This guide explains how to publish the Laravel CMS Renderer package to Packagist.

## Prerequisites

1. A GitHub account
2. A Packagist account (https://packagist.org)

---

## Step 1: Create GitHub Repository

1. Go to https://github.com/new
2. Create a new repository named `cms-renderer-laravel`
3. Make it public

---

## Step 2: Push Code to GitHub

```bash
cd "d:/Blog Library Code/cms-renderer-laravel"

# Initialize git
git init

# Add all files
git add .

# Commit
git commit -m "Initial release v1.0.0"

# Add remote origin (replace with your GitHub username)
git remote add origin https://github.com/talhakazmi/cms-renderer-laravel.git

# Push to GitHub
git branch -M main
git push -u origin main
```

---

## Step 3: Create a Release Tag

```bash
# Create a version tag
git tag v1.0.0

# Push the tag
git push origin v1.0.0
```

---

## Step 4: Register on Packagist

1. Go to https://packagist.org
2. Sign in with GitHub
3. Click **"Submit"** in the top menu
4. Enter your GitHub repository URL:
   ```
   https://github.com/talhakazmi/cms-renderer-laravel
   ```
5. Click **"Check"**
6. Click **"Submit"**

---

## Step 5: Set Up Auto-Update (Recommended)

1. In Packagist, go to your package page
2. Click **"Edit"** or **"Update"**
3. Copy the **Packagist API Token**
4. Go to your GitHub repo → **Settings** → **Webhooks**
5. Click **"Add webhook"**
6. Set:
   - **Payload URL**: `https://packagist.org/api/update-package?username=talhakazmi`
   - **Content type**: `application/json`
   - **Secret**: (your Packagist API token)
   - **Events**: Just the push event
7. Click **"Add webhook"**

Now Packagist will auto-update when you push new tags!

---

## Step 6: Verify Installation

Test the package in a Laravel project:

```bash
# Create a test Laravel project
composer create-project laravel/laravel test-cms-laravel
cd test-cms-laravel

# Install your package
composer require talhakazmi/cms-renderer

# Run the install command
php artisan cms-renderer:install

# Start the server
php artisan serve
```

Visit http://localhost:8000/blog to see if it works!

---

## Updating the Package

To release a new version:

```bash
# Make your changes
git add .
git commit -m "Fix: description of changes"

# Create a new version tag
git tag v1.0.1

# Push changes and tag
git push origin main
git push origin v1.0.1
```

Packagist will automatically pick up the new version.

---

## Version Numbering (Semantic Versioning)

- `v1.0.0` → Major version (breaking changes)
- `v1.1.0` → Minor version (new features, backward compatible)
- `v1.0.1` → Patch version (bug fixes)

---

## Package URL

After publishing, your package will be available at:

```
https://packagist.org/packages/talhakazmi/cms-renderer
```

Users can install it with:

```bash
composer require talhakazmi/cms-renderer
```

---

## Troubleshooting

### "Package not found"
- Make sure the repository is public
- Wait a few minutes for Packagist to index
- Try clicking "Update" on your Packagist package page

### "Invalid composer.json"
- Run `composer validate` in the package directory
- Fix any reported issues

### Webhook not working
- Check the webhook delivery logs in GitHub
- Verify the API token is correct
