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
5. Open **Pages → Home** to edit the banner and section text (Classic Editor + numbered boxes). Logo: **Appearance → Customize**. Menus: **Appearance → Menus**. Site-wide email/footer/SEO: **Trinetix Settings**.
6. Visit **Settings → Permalinks** and click **Save** if archive URLs 404.

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
    ├── classic-editor.php    # Classic Editor for pages
    ├── page-sections.php     # Home page section meta + migration
    ├── shortcodes.php        # [trinetix_industries], [trinetix_work], …
    ├── admin-settings.php    # Slim globals (not homepage copy)
    ├── contact-handler.php
    ├── seo.php / schema.php / security.php
    └── demo-content.php
├── templates/page-sections.php  # Optional “Page Sections” template
└── screenshot.png            # Appearance → Themes preview
```

## Where to edit content (non-technical)

| What | Where |
|------|--------|
| Logo, header menu, CTA button, footer | **Appearance → Header & Footer** |
| Menu link items | Appearance → Menus |
| Home banner, intro, section titles, contact section text | **Pages → Home** — set Template to **Home**, then use boxes 1–4 |
| Show/hide header or footer on a page | Edit page → sidebar **Header & Footer** |
| Industries / Our Work lists on any page | Shortcodes `[trinetix_industries]` / `[trinetix_work]` in the Classic Editor |
| Service / industry / case study cards | Left admin menus (Services, Industries, Case Studies, …) |
| Contact email, form recipient, footer copyright, SEO | Trinetix Settings |

## Settings API (do not redefine defaults differently)

Use helpers from `inc/helpers.php`:

- `trinetix_get_setting( $key, $default )` — site-wide option
- `trinetix_home_setting( $key, $default )` — Home page meta first, then settings
- `trinetix_get_settings()` / `trinetix_default_settings()`
- `trinetix_asset_uri()` / `trinetix_asset_path()` / `trinetix_asset_version()`
- `trinetix_get_logo_html()` — returns a full `<a class="brand">…</a>`; use **alone** in the header (do not wrap again)
- `trinetix_arrow_icon( $variant )`
- `trinetix_ordered_query( $post_type, $args )`
- `trinetix_default_hero_capabilities()` / `trinetix_capability_icon()`
- `trinetix_split_accent_title()`
- `Trinetix_Flat_Nav_Walker` — primary nav outputs bare `<a>` tags for the extracted CSS

Admin UI: `inc/admin-settings.php` stores **global** values in the `trinetix_settings` option. Homepage copy lives on the Home page meta (`inc/page-sections.php`).

## Homepage sections

`front-page.php` loads (fixed order):

1. Hero (video from Home page fields or theme assets)
2. Intro + Approach cards
3. Services
4. Industries (tabs driven by `trinetixIndustries` localized JSON) — also `[trinetix_industries]`
5. Case studies / Work — also `[trinetix_work]`
6. Testimonials (`trinetixTestimonials`)
7. Partners
8. Knowledge Hub
9. Contact (`#trinetixContactForm` → AJAX `trinetix_contact`)

Empty CPT queries fall back to reference copy so the layout still renders before editors publish content.

## Shortcodes

| Shortcode | Section |
|-----------|---------|
| `[trinetix_industries]` | Industry expertise slider |
| `[trinetix_work]` | Our Work / case studies |
| `[trinetix_services]` | Services grid |
| `[trinetix_testimonials]` | Testimonials |
| `[trinetix_partners]` | Partners |
| `[trinetix_knowledge]` | Knowledge Hub |
| `[trinetix_approach]` | Approach cards |
| `[trinetix_contact]` | Contact form section |
| `[trinetix_hero]` / `[trinetix_intro]` | Hero / intro (advanced reuse) |

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
- `footer_company`, `footer_legal` (rendered in footer when assigned)

Footer rows expect **parent items as column titles** and **children as links**.

## Assets

Do not delete `assets/css` or `assets/js`. Enqueue uses filemtime versioning. Homepage scripts:

- `navigation.js`, `main.js` — all pages (search overlay in `navigation.js`)
- `hero-video.js`, `sliders.js`, `contact.js` — front page

## Development tips

- Match class names from `_extract/body-clean.html` when changing markup.
- Prefer `trinetix_home_setting()` for homepage copy and `trinetix_get_setting()` for globals.
- After adding CPT rewrite changes, flush permalinks.
- Replace Unsplash fallback images by setting featured images on CPT items.
- Pages use the **Classic Editor** by default (`inc/classic-editor.php`).

## License

Proprietary — Trinetix Consulting LLC. All rights reserved.

