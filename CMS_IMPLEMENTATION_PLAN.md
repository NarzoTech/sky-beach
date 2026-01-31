# CMS Implementation Plan for Website Module

## Overview

This document outlines the complete plan for implementing a Content Management System (CMS) for the Sky Beach website. The CMS will allow administrators to manage all static content without editing code.

---

## Table of Contents

1. [Database Structure](#database-structure)
2. [Content Analysis by Page](#content-analysis-by-page)
3. [Admin Panel Structure](#admin-panel-structure)
4. [Helper Functions](#helper-functions)
5. [Implementation Phases](#implementation-phases)
6. [Files to Modify](#files-to-modify)
7. [Priority Matrix](#priority-matrix)

---

## Database Structure

### 1. Site Settings Table

Stores global site configuration.

```sql
CREATE TABLE site_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) UNIQUE NOT NULL,
    value JSON,
    `group` VARCHAR(100) DEFAULT 'general',
    type VARCHAR(50) DEFAULT 'text',
    label VARCHAR(255),
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Groups:**
- `general` - Site name, logo, favicon, tagline
- `contact` - Address, phone numbers, emails
- `social` - Facebook, Twitter, Instagram, LinkedIn URLs
- `hours` - Business operating hours
- `seo` - Default meta titles, descriptions

**Example Data:**
```json
{
    "key": "contact.phone_primary",
    "value": "+880 1234-567890",
    "group": "contact",
    "type": "text",
    "label": "Primary Phone Number"
}
```

---

### 2. Page Sections Table

Manages dynamic content sections for each page.

```sql
CREATE TABLE page_sections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(100) NOT NULL,
    section_key VARCHAR(100) NOT NULL,
    title VARCHAR(500),
    subtitle VARCHAR(500),
    content TEXT,
    image VARCHAR(500),
    background_image VARCHAR(500),
    button_text VARCHAR(255),
    button_link VARCHAR(500),
    extra_data JSON,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_page_section (page, section_key)
);
```

**Pages & Sections:**

| Page | Section Key | Description |
|------|-------------|-------------|
| home | hero_banner | Main hero section |
| home | category_section | Category heading |
| home | promo_large | Large promotional banner |
| home | promo_small | Small promotional banner |
| home | special_offer | Special offer full-width banner |
| home | app_download | App download section |
| home | menu_section | Menu section heading |
| home | blog_section | Blog section heading |
| about | story | About us story content |
| about | showcase | Showcase images section |
| contact | form_section | Contact form heading |
| contact | map | Google Maps embed |
| reservation | info_section | Reservation info heading |
| catering | intro | Catering intro section |
| catering | packages_heading | Packages section heading |
| catering | event_types | Event types heading |

---

### 3. Testimonials Table

```sql
CREATE TABLE testimonials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255),
    company VARCHAR(255),
    content TEXT NOT NULL,
    image VARCHAR(500),
    rating TINYINT DEFAULT 5,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

### 4. Counters Table

```sql
CREATE TABLE counters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(255) NOT NULL,
    value INT NOT NULL,
    icon VARCHAR(255),
    suffix VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Default Data:**
| Label | Value | Icon |
|-------|-------|------|
| Dishes | 45 | fas fa-utensils |
| Locations | 68 | fas fa-map-marker-alt |
| Chefs | 32 | fas fa-user-tie |
| Cities | 120 | fas fa-city |

---

### 5. Promotional Banners Table

```sql
CREATE TABLE promotional_banners (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    title VARCHAR(500),
    subtitle VARCHAR(500),
    description TEXT,
    image VARCHAR(500),
    background_image VARCHAR(500),
    button_text VARCHAR(255),
    button_link VARCHAR(500),
    position VARCHAR(100) NOT NULL,
    badge_text VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    start_date DATE,
    end_date DATE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Positions:**
- `home_large` - Home page large banner
- `home_small` - Home page small banner
- `home_full` - Home page full-width special offer
- `sidebar` - Sidebar promotional banner (used on service details, etc.)
- `menu_page` - Menu page promotional banner

---

### 6. Legal Pages Table

```sql
CREATE TABLE legal_pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(500) NOT NULL,
    content LONGTEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Default Pages:**
- `privacy-policy` - Privacy Policy
- `terms-conditions` - Terms & Conditions
- `refund-policy` - Refund Policy (optional)
- `cookie-policy` - Cookie Policy (optional)

---

### 7. Gallery/Showcase Images Table

```sql
CREATE TABLE gallery_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    image VARCHAR(500) NOT NULL,
    category VARCHAR(100),
    page VARCHAR(100),
    alt_text VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Categories:**
- `about_showcase` - About page showcase images
- `about_story` - About page story images
- `home_gallery` - Home page gallery (if any)

---

### 8. Info Cards Table

```sql
CREATE TABLE info_cards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    icon VARCHAR(255),
    icon_image VARCHAR(500),
    link VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Usage:**
- Reservation page info cards (Opening Hours, Call Us, Cancellation Policy)
- Contact page info cards
- Any feature/info cards on other pages

---

### 9. Event Types Table (for Catering)

```sql
CREATE TABLE event_types (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    icon VARCHAR(255),
    description TEXT,
    image VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Default Data:**
- Wedding
- Corporate
- Birthday
- Anniversary
- Graduation
- Other

---

### 10. Features Table (Reusable)

```sql
CREATE TABLE features (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(100) NOT NULL,
    section VARCHAR(100),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(255),
    image VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Usage:**
- Catering features (Customizable Menus, Professional Staff, etc.)
- Service features
- Any bullet-point feature lists

---

## Content Analysis by Page

### Home Page (`index.blade.php`)

| Section | Current Content | CMS Table | Fields |
|---------|-----------------|-----------|--------|
| Hero Banner | "Delicious Food", "Special Foods for your Eating" | page_sections | title, subtitle, content, image, background_image, button_text, button_link |
| Category Section | "Our Popular category" | page_sections | title |
| Category Items | Pizza, Dessert, Burger, etc. | *Use existing menu_categories table* | - |
| Large Promo Banner | "The best Burger place in town" | promotional_banners | title, image (position: home_large) |
| Small Promo Banner | "Great Value Mixed Drinks" | promotional_banners | title, image (position: home_small) |
| Special Offer | "Today special offer", "Delicious Food with us" | promotional_banners | badge_text, title, background_image, image (position: home_full) |
| Menu Section | "Delicious Menu" | page_sections | title |
| App Download | "Are you Ready to Start your Order?" | page_sections | title, content, image, extra_data (app_store_link, play_store_link) |
| Testimonials | Multiple testimonials | testimonials | name, position, content, image, rating |
| Counters | 45 Dishes, 68 Locations, etc. | counters | label, value, icon |
| Blog Section | "Our Latest News & Article" | page_sections | title |

---

### About Page (`about.blade.php`)

| Section | Current Content | CMS Table | Fields |
|---------|-----------------|-----------|--------|
| Story Section | "We invite you to visit our restaurant" + paragraphs | page_sections | title, content (JSON for multiple paragraphs) |
| Story Images | 4 images | gallery_images | image (category: about_story) |
| Showcase Images | 4 images | gallery_images | image (category: about_showcase) |
| Testimonials | Same as home | testimonials | - |
| Counters | Same as home | counters | - |
| Chefs Section | "Meet Our Special Chefs" | page_sections | title |
| Blog Section | "Our Latest News & Article" | page_sections | title |

---

### Contact Page (`contact.blade.php`)

| Section | Current Content | CMS Table | Fields |
|---------|-----------------|-----------|--------|
| Form Heading | "Get In Touch" | page_sections | title |
| Form Image | contact_img.jpg | page_sections | image |
| Address Card | "16/A, Romadan House..." | site_settings | contact.address |
| Phone Card | "+990 123 456 789" | site_settings | contact.phone_primary, contact.phone_secondary |
| Email Card | "info@skybeach.com" | site_settings | contact.email_primary, contact.email_secondary |
| Google Map | Iframe embed | site_settings | contact.google_map_embed |

---

### Reservation Page (`reservation.blade.php`)

| Section | Current Content | CMS Table | Fields |
|---------|-----------------|-----------|--------|
| Reservation Image | reservation_img_2.jpg | page_sections | image |
| Info Card: Hours | "Monday - Sunday, 10:00 AM - 10:00 PM" | info_cards | title, content, icon |
| Info Card: Phone | "+1 234 567 8900" | info_cards | title, content, icon |
| Info Card: Policy | "Free cancellation up to 2 hours..." | info_cards | title, content, icon |

---

### Catering Page (`catering/index.blade.php`)

| Section | Current Content | CMS Table | Fields |
|---------|-----------------|-----------|--------|
| Intro Section | "Make Your Event Unforgettable" | page_sections | title, content, image |
| Features | Customizable Menus, Professional Staff, etc. | features | title, icon (page: catering) |
| Event Types | Wedding, Corporate, Birthday, etc. | event_types | name, icon |
| Packages Heading | "Featured Packages", "Our Catering Packages" | page_sections | title, subtitle |

---

### Legal Pages (`privacy_policy.blade.php`, `terms_condition.blade.php`)

| Page | CMS Table | Fields |
|------|-----------|--------|
| Privacy Policy | legal_pages | title, content (rich text) |
| Terms & Conditions | legal_pages | title, content (rich text) |

---

### Service Details (`service_details.blade.php`)

| Section | Current Content | CMS Table | Fields |
|---------|-----------------|-----------|--------|
| Sidebar Promo | "Get Up to 50% Off", "Special Combo Pack" | promotional_banners | title, subtitle, image (position: sidebar) |

---

## Admin Panel Structure

```
Modules/CMS/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── SiteSettingController.php
│   │       ├── PageSectionController.php
│   │       ├── TestimonialController.php
│   │       ├── CounterController.php
│   │       ├── PromotionalBannerController.php
│   │       ├── LegalPageController.php
│   │       ├── GalleryController.php
│   │       ├── InfoCardController.php
│   │       ├── EventTypeController.php
│   │       └── FeatureController.php
│   └── Models/
│       ├── SiteSetting.php
│       ├── PageSection.php
│       ├── Testimonial.php
│       ├── Counter.php
│       ├── PromotionalBanner.php
│       ├── LegalPage.php
│       ├── GalleryImage.php
│       ├── InfoCard.php
│       ├── EventType.php
│       └── Feature.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── site-settings/
│       │   ├── index.blade.php
│       │   └── edit.blade.php
│       ├── page-sections/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── testimonials/
│       ├── counters/
│       ├── banners/
│       ├── legal-pages/
│       ├── gallery/
│       ├── info-cards/
│       ├── event-types/
│       └── features/
└── routes/
    └── web.php
```

### Admin Menu Structure

```
CMS Management
├── Site Settings
│   ├── General Settings
│   ├── Contact Information
│   ├── Social Media Links
│   └── Business Hours
│
├── Page Content
│   ├── Home Page Sections
│   ├── About Page Sections
│   ├── Contact Page Sections
│   ├── Reservation Page Sections
│   └── Catering Page Sections
│
├── Promotional Banners
│   ├── All Banners
│   └── Create New Banner
│
├── Testimonials
│   ├── All Testimonials
│   └── Add New Testimonial
│
├── Counters/Statistics
│   ├── All Counters
│   └── Add New Counter
│
├── Gallery
│   ├── All Images
│   └── Upload Images
│
├── Info Cards
│   ├── All Cards
│   └── Create New Card
│
├── Legal Pages
│   ├── Privacy Policy
│   └── Terms & Conditions
│
└── Catering Settings
    ├── Event Types
    └── Features
```

---

## Helper Functions

Create a helper file: `app/Helpers/CmsHelper.php`

```php
<?php

use Modules\CMS\app\Models\SiteSetting;
use Modules\CMS\app\Models\PageSection;
use Modules\CMS\app\Models\Testimonial;
use Modules\CMS\app\Models\Counter;
use Modules\CMS\app\Models\PromotionalBanner;
use Modules\CMS\app\Models\LegalPage;
use Modules\CMS\app\Models\GalleryImage;
use Modules\CMS\app\Models\InfoCard;
use Modules\CMS\app\Models\EventType;
use Modules\CMS\app\Models\Feature;
use Illuminate\Support\Facades\Cache;

/**
 * Get a site setting value
 */
if (!function_exists('cms_setting')) {
    function cms_setting($key, $default = null)
    {
        return Cache::remember("cms_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = SiteSetting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }
}

/**
 * Get a page section
 */
if (!function_exists('cms_section')) {
    function cms_section($page, $sectionKey)
    {
        return Cache::remember("cms_section_{$page}_{$sectionKey}", 3600, function () use ($page, $sectionKey) {
            return PageSection::where('page', $page)
                ->where('section_key', $sectionKey)
                ->where('is_active', true)
                ->first();
        });
    }
}

/**
 * Get all active testimonials
 */
if (!function_exists('cms_testimonials')) {
    function cms_testimonials($limit = null, $featured = false)
    {
        $cacheKey = "cms_testimonials_{$limit}_{$featured}";
        return Cache::remember($cacheKey, 3600, function () use ($limit, $featured) {
            $query = Testimonial::where('is_active', true)
                ->orderBy('sort_order');

            if ($featured) {
                $query->where('is_featured', true);
            }

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }
}

/**
 * Get all active counters
 */
if (!function_exists('cms_counters')) {
    function cms_counters()
    {
        return Cache::remember('cms_counters', 3600, function () {
            return Counter::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }
}

/**
 * Get promotional banner by position
 */
if (!function_exists('cms_banner')) {
    function cms_banner($position)
    {
        return Cache::remember("cms_banner_{$position}", 3600, function () use ($position) {
            return PromotionalBanner::where('position', $position)
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->orderBy('sort_order')
                ->first();
        });
    }
}

/**
 * Get legal page by slug
 */
if (!function_exists('cms_legal_page')) {
    function cms_legal_page($slug)
    {
        return Cache::remember("cms_legal_{$slug}", 3600, function () use ($slug) {
            return LegalPage::where('slug', $slug)
                ->where('is_active', true)
                ->first();
        });
    }
}

/**
 * Get gallery images
 */
if (!function_exists('cms_gallery')) {
    function cms_gallery($category, $limit = null)
    {
        $cacheKey = "cms_gallery_{$category}_{$limit}";
        return Cache::remember($cacheKey, 3600, function () use ($category, $limit) {
            $query = GalleryImage::where('category', $category)
                ->where('is_active', true)
                ->orderBy('sort_order');

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }
}

/**
 * Get info cards for a page
 */
if (!function_exists('cms_info_cards')) {
    function cms_info_cards($page)
    {
        return Cache::remember("cms_info_cards_{$page}", 3600, function () use ($page) {
            return InfoCard::where('page', $page)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }
}

/**
 * Get event types
 */
if (!function_exists('cms_event_types')) {
    function cms_event_types()
    {
        return Cache::remember('cms_event_types', 3600, function () {
            return EventType::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }
}

/**
 * Get features for a page/section
 */
if (!function_exists('cms_features')) {
    function cms_features($page, $section = null)
    {
        $cacheKey = "cms_features_{$page}_{$section}";
        return Cache::remember($cacheKey, 3600, function () use ($page, $section) {
            $query = Feature::where('page', $page)
                ->where('is_active', true)
                ->orderBy('sort_order');

            if ($section) {
                $query->where('section', $section);
            }

            return $query->get();
        });
    }
}

/**
 * Clear all CMS cache
 */
if (!function_exists('cms_clear_cache')) {
    function cms_clear_cache()
    {
        $keys = [
            'cms_setting_*',
            'cms_section_*',
            'cms_testimonials_*',
            'cms_counters',
            'cms_banner_*',
            'cms_legal_*',
            'cms_gallery_*',
            'cms_info_cards_*',
            'cms_event_types',
            'cms_features_*',
        ];

        // Clear cache - implementation depends on cache driver
        Cache::flush(); // Or use tags if using Redis
    }
}
```

---

## Implementation Phases

### Phase 1: Foundation (Week 1)

**Tasks:**
1. Create CMS module structure
2. Create all database migrations
3. Create Eloquent models
4. Create seeders with default content
5. Register helper functions

**Deliverables:**
- [ ] All migrations created and run
- [ ] All models with relationships
- [ ] Seeders with current hardcoded content
- [ ] Helper functions registered in composer.json

---

### Phase 2: Admin CRUD (Week 2)

**Tasks:**
1. Create admin controllers
2. Create admin views with forms
3. Implement image upload handling
4. Add TinyMCE/CKEditor for rich text
5. Add validation rules

**Deliverables:**
- [ ] Site Settings CRUD
- [ ] Page Sections CRUD
- [ ] Testimonials CRUD
- [ ] Counters CRUD
- [ ] Promotional Banners CRUD
- [ ] Legal Pages CRUD
- [ ] Gallery CRUD
- [ ] Info Cards CRUD
- [ ] Event Types CRUD
- [ ] Features CRUD

---

### Phase 3: Frontend Integration (Week 3)

**Tasks:**
1. Update `index.blade.php` (Home)
2. Update `about.blade.php`
3. Update `contact.blade.php`
4. Update `reservation.blade.php`
5. Update `privacy_policy.blade.php`
6. Update `terms_condition.blade.php`
7. Update `catering/index.blade.php`
8. Update `service_details.blade.php`
9. Update header/footer partials

**Deliverables:**
- [ ] All blade files updated to use CMS helpers
- [ ] Fallback to default values if CMS content missing
- [ ] Cache implemented for performance

---

### Phase 4: Testing & Optimization (Week 4)

**Tasks:**
1. Test all CMS functions
2. Test cache clearing
3. Performance optimization
4. Documentation
5. User training materials

**Deliverables:**
- [ ] All features tested
- [ ] Performance benchmarks met
- [ ] Admin user guide created

---

## Files to Modify

### Blade Templates

| File | Priority | Sections to Update |
|------|----------|-------------------|
| `index.blade.php` | High | Hero, categories, promos, counters, testimonials, app download, blog heading |
| `about.blade.php` | High | Story, showcase images, counters, testimonials, chef heading, blog heading |
| `contact.blade.php` | High | Contact info cards, map, form heading |
| `reservation.blade.php` | Medium | Info cards, reservation image |
| `privacy_policy.blade.php` | Medium | Full content |
| `terms_condition.blade.php` | Medium | Full content |
| `catering/index.blade.php` | Medium | Intro, features, event types, headings |
| `service_details.blade.php` | Low | Sidebar promo banner |
| `partials/header.blade.php` | Low | Logo, navigation (if dynamic) |
| `partials/footer.blade.php` | Low | Footer content, social links |

---

## Priority Matrix

### High Priority (Must Have)
- Site Settings (contact info, social links)
- Home Page Hero Banner
- Testimonials
- Counters
- Contact Information

### Medium Priority (Should Have)
- Promotional Banners
- Legal Pages (Privacy, Terms)
- About Page Content
- Gallery Images
- Info Cards

### Low Priority (Nice to Have)
- Catering Event Types
- Features Lists
- Dynamic Navigation
- SEO Settings per page

---

## Example Usage in Blade Templates

### Before (Hardcoded):
```blade
<section class="banner" style="background: url({{ asset('website/images/banner_bg.jpg') }});">
    <div class="banner_text">
        <h5>Delicious Food</h5>
        <h1>Special Foods for your Eating</h1>
        <p>Commodo ullamcorper a lacus vestibulum sed arcu non...</p>
        <a class="common_btn" href="#">order now</a>
    </div>
    <div class="banner_img">
        <img src="{{ asset('website/images/banner_img.png') }}" alt="banner">
    </div>
</section>
```

### After (CMS):
```blade
@php
    $heroBanner = cms_section('home', 'hero_banner');
@endphp
<section class="banner" style="background: url({{ $heroBanner->background_image ? asset($heroBanner->background_image) : asset('website/images/banner_bg.jpg') }});">
    <div class="banner_text">
        <h5>{{ $heroBanner->subtitle ?? 'Delicious Food' }}</h5>
        <h1>{{ $heroBanner->title ?? 'Special Foods for your Eating' }}</h1>
        <p>{{ $heroBanner->content ?? 'Default description text...' }}</p>
        <a class="common_btn" href="{{ $heroBanner->button_link ?? '#' }}">
            {{ $heroBanner->button_text ?? 'Order Now' }}
        </a>
    </div>
    <div class="banner_img">
        <img src="{{ $heroBanner->image ? asset($heroBanner->image) : asset('website/images/banner_img.png') }}" alt="banner">
    </div>
</section>
```

### Testimonials Example:
```blade
@foreach(cms_testimonials(5) as $testimonial)
<div class="single_testimonial">
    <p class="rating">
        @for($i = 1; $i <= $testimonial->rating; $i++)
            <i class="fas fa-star"></i>
        @endfor
    </p>
    <p class="description">"{{ $testimonial->content }}"</p>
    <div class="single_testimonial_footer">
        <div class="img">
            <img src="{{ $testimonial->image ? asset($testimonial->image) : asset('website/images/default_avatar.png') }}" alt="{{ $testimonial->name }}">
        </div>
        <h3>{{ $testimonial->name }} <span>{{ $testimonial->position }}</span></h3>
    </div>
</div>
@endforeach
```

### Counters Example:
```blade
@foreach(cms_counters() as $counter)
<div class="col-lg-3 col-sm-6 wow fadeInUp">
    <div class="single_counter">
        <h2 class="counter">{{ $counter->value }}</h2>
        <span>{{ $counter->label }}</span>
    </div>
</div>
@endforeach
```

---

## Notes

1. **Caching**: All CMS content should be cached to avoid database queries on every page load. Clear cache when content is updated in admin panel.

2. **Image Storage**: Store images in `public/uploads/cms/` directory with subdirectories for each type (banners, testimonials, gallery, etc.).

3. **Fallback Values**: Always provide fallback values in blade templates for when CMS content is missing.

4. **Rich Text Editor**: Use TinyMCE or CKEditor for legal pages and any content that needs HTML formatting.

5. **Validation**: Implement proper validation in admin forms (image dimensions, file types, required fields).

6. **Soft Deletes**: Consider using soft deletes for all CMS models to prevent accidental data loss.

7. **Activity Logging**: Log all CMS changes for audit trail.

---

## Estimated Timeline

| Phase | Duration | Dependencies |
|-------|----------|--------------|
| Phase 1: Foundation | 1 week | None |
| Phase 2: Admin CRUD | 1 week | Phase 1 |
| Phase 3: Frontend Integration | 1 week | Phase 2 |
| Phase 4: Testing | 1 week | Phase 3 |
| **Total** | **4 weeks** | |

---

*Document created: January 2026*
*Last updated: January 2026*
