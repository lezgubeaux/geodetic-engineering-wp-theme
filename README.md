# VerQuin — custom WordPress theme

A purpose-built classic WordPress theme for a geodetic-engineering company's 2026 site rebuild. No starter theme, no page builder, no framework: every template, style rule and script in this repository was written for this project.

Pages are composed by editors from a fixed vocabulary of sections, rows and blocks defined in ACF Pro Flexible Content — a structured content system rather than a free-form editor.

**Status:** in development. The theme renders and is being built out page by page; some rows exist as template and CSS before being wired to their fields.

---

## Where to start

| Path | What it shows |
|---|---|
| [`functions.php`](functions.php) · [`inc/setup.php`](inc/setup.php) | Architecture and initialization. `functions.php` is a loader only; every behaviour lives in a single-responsibility file under `inc/`. |
| [`inc/acf.php`](inc/acf.php) · [`template-parts/`](template-parts/) | The structured content system: local-JSON field sync, the Options page, and the Flexible Content dispatcher that turns editor layouts into section and row templates. |
| [`inc/blocks/`](inc/blocks/) · [`inc/helpers.php`](inc/helpers.php) | Custom WordPress integration: reusable render functions with typed parameters, escaping helpers, and a strict data contract between layers. |
| [`assets/css/theme.css`](assets/css/theme.css) · [`assets/js/theme.js`](assets/js/theme.js) | Frontend component and asset organization: tokens, mobile-first BEM with owner prefixes, banner-ordered file, one gap owner per gap; one deferred JS file with one IIFE per owner. |
| [`inc/enqueue.php`](inc/enqueue.php) · [`inc/charts.php`](inc/charts.php) | Performance and integration detail: `filemtime` cache-busting, deferred scripts, and 280 KB of D3 enqueued from the row itself so it loads only on pages that actually carry a chart. |

## Architecture

The theme is built in four layers. Each has a fixed location, a fixed naming prefix, and a one-way dependency.

| Layer | What it is | Location | CSS owner |
|---|---|---|---|
| Section | One full-width strip of a page (`page-head`, `band`, `form`) | `template-parts/layouts/` | `.s-<slug>` |
| Row | One content row inside a band (text-media, cards, gallery, spec-tables, …) | `template-parts/rows/` | `.r-<slug>` |
| Block | A PHP function rendering one reusable fragment from parameters | `inc/blocks/` | `.b-<name>` |
| Global | Tokens, base, type scale, shell, buttons, header, footer, CTA band | `header.php`, `footer.php`, `inc/`, top of `theme.css` | unprefixed |

A section may call rows, blocks and globals. A row may call blocks and globals, never another row. A block may call blocks and globals, never a section or row, and **never reads ACF or post data** — it receives everything as typed parameters. `page.php` holds no markup of its own: it renders the page head, then dispatches the page's layouts.

```
body
 ├─ header.site-header        global
 ├─ main
 │   ├─ section.s-page-head   fixed slot, always first
 │   ├─ section.s-band        repeatable; holds rows
 │   │    └─ div.wrap
 │   │         ├─ div.r-text-media
 │   │         └─ div.r-cards
 │   └─ section.s-form
 ├─ section.cta-band          global
 └─ footer.site-footer        global
```

## Conventions

- **PHP 8.1**, typed parameters and return types throughout; WordPress Coding Standards; every file opens with a docblock and `defined( 'ABSPATH' ) || exit;`.
- **Escaping at the point of output**, with three audited helpers (`verquin_heading()`, `verquin_rich_text()`, `verquin_classes()`) as the only places a `phpcs:ignore` for escaping is permitted.
- **Empty or invalid input renders nothing**, so callers invoke blocks unconditionally.
- **Graceful degradation**: every ACF entry point is guarded, so the theme still renders with ACF Pro deactivated. Contact Form 7 output is suppressed rather than leaking a raw shortcode when the plugin is inactive.
- **Field definitions travel with the code** — ACF local JSON is saved into `acf-json/` and version-controlled, so no database export is needed to move the site.
- **Mobile-first CSS**: base rules are the small-screen state; every `@media` block lives in one ascending `Breakpoints` section at the end of `theme.css`. `max-width` is never used.
- **One name, one owner, one place.** A class needed by two owners is promoted to a block or a global, never duplicated. A component registry outside this repository is the authoritative name list.

## Stack and dependencies

- WordPress 6.5+, PHP 8.1+, classic theme (no `theme.json`, no block editor for pages)
- ACF Pro — page composition, Options page, local JSON
- Contact Form 7 — forms, with the reCAPTCHA v3 disclaimer emitted only when the integration is actually configured
- D3 v7 (`assets/js/vendor/`) — the legacy site's GNSS visualisations, carried over and re-housed; chart CSVs are read from the uploads folder so re-exported data never requires a theme deploy

No build step, no npm or Composer dependency, no CDN resource. Prettier formats CSS/JS/JSON/Markdown; PHP is written by hand to WPCS.

## Notes on this copy

This repository is published as a work sample. The code is the code that runs; client-owned subject matter has been redacted, and each redaction is marked where it occurs:

- **Technical drawings removed.** The five SVGs in `assets/svg/` are placeholders. The diagram registry in [`inc/blocks/diagram.php`](inc/blocks/diagram.php), the render path and the scroll/accessibility behaviour are unchanged.
- **Station identifiers redacted.** The GNSS station registry in [`inc/charts.php`](inc/charts.php) uses `XXXX1`–`XXXX8` in place of the real station codes. The registry's shape, the mix of chart kinds and the annotation flags are as built.
- **Reference material left out.** The legacy data pages used during chart migration are not included.
- No client media, page copy, credentials or database content is present. `assets/css/framework-reference.css` is the remainder of the client-supplied reference stylesheet, enqueued before `theme.css` and shrinking as components are migrated; it is removed at CSS close-out.

The theme is written for one client's site and is not intended for reuse. Published for review; all rights reserved.

## About the work

I used AI extensively for analysis, scaffolding, debugging and review, while retaining responsibility for architecture, validation and final implementation decisions.
