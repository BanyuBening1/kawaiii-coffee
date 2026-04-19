# 💾 Storage Configuration & Setup Guide

## ⚙️ Critical Step: Create Storage Link

**THIS STEP IS MANDATORY!** Without this, images won't display.

### What is Storage Link?

```
Without symlink:
  File: storage/app/public/products/image.jpg
  URL: /storage/products/image.jpg
  Result: ❌ 404 Not Found

With symlink:
  File: storage/app/public/products/image.jpg
  Symlink: public/storage → storage/app/public
  URL: /storage/products/image.jpg
  Result: ✅ Image displays
```

---

## 🚀 Setup Instructions

### STEP 1: Create Symlink

**On Mac/Linux:**

```bash
php artisan storage:link
```

**On Windows (Command Prompt as Administrator):**

```batch
php artisan storage:link
```

**If that fails, use PowerShell (as Administrator):**

```powershell
cmd /c mklink /d "public\storage" "storage\app\public"
```

**Verify it worked:**

```bash
# Mac/Linux
ls -la public/storage

# Windows PowerShell
cmd /c dir public\storage

# You should see: storage → storage/app/public
```

---

### STEP 2: Create Storage Directories

```bash
# Create products directory if it doesn't exist
mkdir -p storage/app/public/products

# Set proper permissions (Linux/Mac)
chmod -R 755 storage/app/public
```

---

### STEP 3: Verify Storage Configuration

Check `config/filesystems.php`:

```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL') . '/storage',
        'visibility' => 'public',
    ],
]
```

If not configured, add it manually.

---

## 🧪 Test Storage Setup

### Test 1: Check Symlink Exists

```bash
ls -la public/storage
# Result: storage → /path/to/storage/app/public
```

### Test 2: Create Test File

```bash
echo "test" > storage/app/public/test.txt
```

### Test 3: Access via Browser

```
http://localhost:8000/storage/test.txt
```

**Expected:** See "test" content  
**If 404:** Symlink not working, redo STEP 1

---

## 📤 How Images Are Stored

### When User Uploads Image via Forms

**Process:**

```
1. User selects file in form
2. Filament processes upload
3. File saved to: storage/app/public/products/[name].jpg
4. Path stored in DB: "products/image-name.jpg"
5. Filament retrieves via: /storage/products/image-name.jpg
```

### Example Directory Structure

```
storage/
└── app/
    └── public/
        ├── products/
        │   ├── image1.jpg
        │   ├── image2.jpg
        │   └── image3.jpg
        └── categories/
            └── icon1.svg

public/
└── storage/  ← Symlink pointing to storage/app/public
    ├── products/
    ├── categories/
    └── ...
```

---

## 🔐 File Permissions

### Linux/Mac

```bash
# Set permissions so Laravel can write
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# If you get permission errors:
chmod -R 777 storage/
chmod -R 777 bootstrap/cache/

# Better approach - set ownership:
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data bootstrap/cache/
```

### Windows

Windows handles permissions differently. Usually automatic, but if issues:

```powershell
# Right-click folder → Properties → Security
# Grant "Full Control" to current user for:
# - storage/app/public
# - bootstrap/cache
```

---

## 🖨️ Image Upload Configuration

In ProductsForm.php:

```php
FileUpload::make('image')
    ->disk('public')              // Use public disk
    ->directory('products')       // Organize in products folder
    ->visibility('public')        // Make publicly accessible
    ->image()                     // Validate as image
    ->maxSize(2048)              // Max 2MB
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
```

**Result:** File saved to `storage/app/public/products/[hash].jpg`

---

## 🌐 Environment Configuration

### Development (.env)

```env
APP_URL=http://localhost:8000
FILESYSTEM_DISK=public
```

### Production (.env)

```env
APP_URL=https://yoursite.com
FILESYSTEM_DISK=public
# Or if using S3/CDN:
FILESYSTEM_DISK=s3
AWS_URL=https://cloudfront-id.cloudfront.net
```

---

## 🚨 Troubleshooting

### Issue 1: Image Upload Works But No Display

**Problem:** File exists but returns 404

**Solution:**

```bash
# 1. Verify symlink
ls -la public/storage

# 2. Verify file exists
ls -la storage/app/public/products/

# 3. Check file permissions
chmod 644 storage/app/public/products/*

# 4. Clear config cache
php artisan config:clear
php artisan cache:clear
```

---

### Issue 2: Permission Denied When Uploading

**Problem:** `storage/app/public` is not writable

**Solution:**

```bash
# Mac/Linux
chmod -R 755 storage/
sudo chown -R $(whoami) storage/

# Or use web server user
sudo chown -R www-data:www-data storage/
```

---

### Issue 3: Symlink Creation Fails

**Problem:** `php artisan storage:link` doesn't work

**Solution on Windows:**

```powershell
# Run PowerShell as Administrator
# Then run:
cmd /c mklink /d "C:\path\to\public\storage" "C:\path\to\storage\app\public"

# Or using PowerShell directly:
New-Item -ItemType SymbolicLink -Path "public\storage" -Target "storage\app\public" -Force
```

---

### Issue 4: Images Display at Wrong Size

**Problem:** ImageColumn shows full-size image despite height/width

**Solution:**

1. Clear browser cache: `Ctrl+Shift+Delete`
2. Hard refresh with `Ctrl+Shift+R`
3. Check ProductsTable has:
    ```php
    ->height(50)
    ->width(50)
    ->square()
    ```

---

## 📊 Storage Disk Comparison

### Option 1: Local Public Disk (Current ✅)

```php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL') . '/storage',
]

// Pros:
// ✅ Simple setup
// ✅ No cost
// ✅ Files on same server
// ✅ Good for small projects

// Cons:
// ❌ Server storage usage
// ❌ doesn't scale to multiple servers
// ❌ No CDN acceleration
```

### Option 2: S3 + CloudFront (Production)

```php
's3' => [
    'driver' => 's3',
    'key' => env('AWS_ACCESS_KEY_ID'),
    'secret' => env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('AWS_DEFAULT_REGION'),
    'bucket' => env('AWS_BUCKET'),
    'url' => env('AWS_URL'),  // CloudFront URL
]

// Pros:
// ✅ Scales infinitely
// ✅ CDN acceleration
// ✅ Doesn't use server storage
// ✅ Cheap at scale
// ✅ Professional

// Cons:
// ❌ Setup more complex
// ❌ AWS costs
// ❌ Needs credentials management
```

---

## 🎯 Production Checklist

### Before Going Live

- [ ] Storage link created
- [ ] storage/app/public directory exists & writable
- [ ] Test image upload works
- [ ] Test image displays via /storage/...
- [ ] File permissions set correctly
- [ ] .env configured for production
- [ ] Config cached: `php artisan config:cache`
- [ ] Routes cached: `php artisan route:cache`

### Production Best Practices

1. **Use S3 or CDN** for image delivery

    ```bash
    composer require league/flysystem-aws-s3-v3
    ```

2. **Implement image optimization**

    ```bash
    composer require intervention/image
    ```

3. **Setup automated backups** for images in S3

4. **Monitor storage usage**
    ```bash
    df -h storage/app/public
    ```

---

## 📋 Quick Reference

### Create Symlink

```bash
php artisan storage:link
```

### Check If Working

```bash
# Create test file
echo "test" > storage/app/public/test.txt

# Should be accessible at:
http://localhost:8000/storage/test.txt
```

### Fix Permissions

```bash
chmod -R 755 storage/
```

### Clear Cache (if images not updating)

```bash
php artisan cache:clear
php artisan config:clear
```

---

## ✅ Verification Steps

After setup, verify everything works:

```
1. Run: php artisan storage:link
2. Open: http://localhost:8000/admin/products
3. Upload an image
4. Check file exists: storage/app/public/products/[name]
5. Image should display as 50x50 thumbnail
6. URL should be: /storage/products/[name]
7. DevTools shows image loaded successfully
```

**If all ✅, you're good to go!**

---

## 🎉 Summary

### What You Need to Do:

1. Run `php artisan storage:link`
2. Test by uploading image
3. Verify image displays

### That's it!

The rest is automatic. Filament handles:

- File storage location
- File access via URL
- Image rendering
- Lazy loading

### Storage Info:

- Location: `storage/app/public/products/`
- Access: `/storage/products/...`
- Public: Yes (anyone can see)
- Backup: Should backup `storage/app/public/` regularly

---

**Status: READY FOR IMAGES!** 🚀

Run `php artisan storage:link` and start uploading!
