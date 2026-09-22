# Trinetix Consultancy LLC — WordPress Theme

Custom WordPress theme for **Trinetix Consulting LLC**, converted from the approved static HTML design into a fully dynamic custom theme. Frontend class names and visual CSS match the approved homepage.

## Requirements

- WordPress 6.0+
- PHP 8.0+
- Writable uploads (for media / hero video)

## Installation

1. Copy this folder into `wp-content/themes/trinetix-consultancy-llc` (or zip and upload via **Appearance → Themes**).
2. Activate **Trinetix Consultancy LLC**.
3. On first activation the theme seeds demo content once (`trinetix_initial_content_seeded`). It never overwrites existing CPT posts, menus already assigned to theme locations, or an already-configured static front page.
4. Visit **Settings → Permalinks** and click **Save** if archive URLs 404.
5. Open **Appearance → Customize** or the theme settings screen to replace placeholder copy, logos, and media.

## What gets seeded (once)

| Item | Notes |
|------|--------|
| Pages | Home, Approach, Services, Industries, Our Work, Knowledge Hub, Contact, Privacy Policy, Terms & Conditions |
| Reading | Sets Home as front page + Knowledge Hub as posts page when still on default “latest posts” |
| CPTs | Approach items, Services, Industries, Case studies, Testimonials, Partners |
| Posts | Sample Knowledge Hub articles + categories |
| Menus | Primary anchors + hierarchical footer directory menus |
| Settings | Privacy/Terms URLs and knowledge CTA URL only if empty |

To re-seed on a clean site, delete the option `trinetix_initial_content_seeded` from `wp_options` (and remove seeded content you no longer want), then switch themes away and back.

## Theme structure

```
trinetix-consultancy-llc/
├── style.css                 # Theme header
├── functions.php             # Bootstrap + includes
├── header.php / footer.php
├── front-page.php            # Homepage composition
├── index.php, home.php, page.php, single.php, single-post.php
├── archive.php, search.php, 404.php, comments.php, searchform.php
├── archive-service.php / single-service.php
├── archive-industry.php / single-industry.php
├── archive-case-study.php / single-case-study.php
├── assets/
│   ├── css/                  # main.css, responsive.css, editor.css
│   ├── js/                   # navigation, main, hero-video, sliders, contact
│   ├── images/               # logo, hero-poster, partner logos
│   ├── icons/                # arrows + partner SVGs
│   └── video/hero.mp4
├── template-parts/
│   ├── home/                 # hero, intro, approach, services, …
│   ├── global/               # page-hero, pagination, footer-fallback
│   └── content/              # cards, page/single/none
└── inc/
    ├── helpers.php           # settings API, logo, walker, utilities
    ├── theme-setup.php
    ├── enqueue.php
    ├── custom-post-types.php
    ├── taxonomies.php
    ├── meta-boxes.php
    ├── media-fields.php
    ├── admin-settings.php
    ├── contact-handler.php
    ├── seo.php / schema.php / security.php
    └── demo-content.php
```

## Settings API (do not redefine defaults differently)

Use helpers from `inc/helpers.php`:

- `trinetix_get_setting( $key, $default )`
- `trinetix_get_settings()`
- `trinetix_default_settings()` — brand, header CTA, hero, intro, section titles, contact, footer, SEO keys
- `trinetix_asset_uri()` / `trinetix_asset_path()` / `trinetix_asset_version()`
- `trinetix_get_logo_html()` — returns a full `<a class="brand">…</a>`; use **alone** in the header (do not wrap again)
- `trinetix_arrow_icon( $variant )`
- `trinetix_ordered_query( $post_type, $args )`
- `trinetix_default_hero_capabilities()` / `trinetix_capability_icon()`
- `trinetix_split_accent_title()`
- `Trinetix_Flat_Nav_Walker` — primary nav outputs bare `<a>` tags for the extracted CSS

Admin UI: `inc/admin-settings.php` stores values in the `trinetix_settings` option.

## Homepage sections

`front-page.php` loads:

1. Hero (video from settings or `assets/video/hero.mp4` + `assets/images/hero-poster.jpg`)
2. Intro + Approach cards
3. Services
4. Industries (tabs driven by `trinetixIndustries` localized JSON)
5. Case studies / Work
6. Testimonials (`trinetixTestimonials`)
7. Partners
8. Knowledge Hub
9. Contact (`#trinetixContactForm` → AJAX `trinetix_contact`)

Empty CPT queries fall back to reference copy so the layout still renders before editors publish content.

## Custom post types

| Type | Archive | Notes |
|------|---------|--------|
| `service` | `/services/` | Card number + excerpt on home |
| `industry` | `/industries/` | Powers homepage industry slider payload |
| `case_study` | `/case-studies/` | Eyebrow / challenge / solution / results meta |
| `testimonial` | (admin only) | Role, chip label, rating |
| `partner` | (admin only) | Card CSS class (`aws`, `microsoft`, …) |
| `approach_item` | single only | Approach grid cards |
| `contact_submission` | private | Stored form submissions |

## Contact form

- Form ID: `trinetixContactForm`
- Fields: `first_name`, `last_name`, `email`, `phone`, `company`, `message`, `interest`, honeypot `website`
- Handler: `inc/contact-handler.php` (nonce `trinetix_contact`, rate limit, email + private CPT)
- Front script: `assets/js/contact.js` (localized as `trinetixContact`)

Interest pills sync into the hidden `#trinetixInterest` field via `assets/js/main.js`.

## Menus

Registered locations (`inc/theme-setup.php`):

- `primary`
- `footer_ai`, `footer_data`, `footer_engineering`, `footer_experience`
- `footer_company`, `footer_legal` (available; bottom legal links also use settings URLs)

Footer rows expect **parent items as column titles** and **children as links**.

## Assets

Do not delete `assets/css` or `assets/js`. Enqueue uses filemtime versioning. Homepage scripts:

- `navigation.js`, `main.js` — all pages
- `hero-video.js`, `sliders.js`, `contact.js` — front page

## Development tips

- Match class names from `_extract/body-clean.html` when changing markup.
- Prefer `trinetix_get_setting()` over any legacy `trinetix_get_option()` calls.
- After adding CPT rewrite changes, flush permalinks.
- Replace Unsplash fallback images by setting featured images on CPT items.

## License

Proprietary — Trinetix Consulting LLC. All rights reserved.
