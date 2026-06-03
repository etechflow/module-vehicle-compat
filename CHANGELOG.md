# Changelog — ETechFlow_VehicleCompat

## [1.1.0] — 2026-06-03 — Amasty-competitor feature set: PDP fitment badge, SEO URLs, customer garage, universal positioning

Four major additions that turn this from "Vehicle Compatibility v1.0"
into a credible competitor to Amasty Product Parts Finder (~$399).
All features are opt-in via admin config — defaults preserve v1.0.x
behaviour exactly, so existing installs see no change unless they
intentionally enable one.

### Added

#### 1. PDP fitment badge
Renders a coloured "Fits: BMW 3 Series 2018-2023" block under the price
on every product detail page where the product has vehicle compatibility
data assigned. The most-requested Amasty-parity feature — signals
"yes this fits your car" right at the purchase-decision moment.

- **New block**: `Block/Product/FitmentBadge` — resolves the product's
  `vehicle_compat_data` JSON attribute against the Make/Model tables,
  formats human-readable strings ("BMW 3 Series 2018-2023" — year
  ranges collapsed when contiguous, listed individually otherwise),
  and de-dupes across `parts_required` entries.
- **New template**: `view/frontend/templates/product/fitment-badge.phtml`
  — inline-styled HTML that survives email clients and theme overrides.
- **New layout**: `view/frontend/layout/catalog_product_view.xml` —
  injects the badge into `product.info.main` after `product.info.price`.
- **Admin config** under *Stores → Configuration → eTechFlow → Vehicle
  Compatibility → PDP Fitment Badge*:
  - Show Fitment Badge on Product Page (Yes/No, default **No**)
  - Badge Prefix Text (default "Fits:" — set to "Compatible with:" or
    "Made for:" for different tones)
  - Badge Style (success/info/warning/neutral — colour treatment)
- **Inline limit**: max 3 vehicles per badge; surplus shown as
  "and N more". Keeps PDP layouts clean for parts that fit dozens of
  vehicles.

#### 2. SEO-friendly URLs
Maps `/parts/bmw/3-series/2020/brake-pads` to the Part Finder Find
action. Massive SEO improvement over query-string URLs — Google ranks
slug-based URLs significantly better, social-share previews look
clean, link sharing is human-readable.

- **New router**: `Controller/Router/FitmentRouter` — implements
  `Magento\Framework\App\RouterInterface`. Matches the configured
  prefix + Make/Model/Year/Part slugs, resolves slugs back to IDs via
  case-insensitive name lookup, forwards to `vehiclecompat/find/index`
  with proper params.
- **New DI**: `etc/frontend/di.xml` — registers the router with
  sortOrder 30 (before CMS router but after standard).
- **Backward-compatible**: when enabled, BOTH old query-string URLs
  AND new path-based URLs work — old shared links don't break.
- **Slug-tolerant**: "3-series" matches "3 Series", "land-rover"
  matches "Land Rover" (case-insensitive, space → dash normalisation).
- **Admin config** under *Stores → Configuration → eTechFlow → Vehicle
  Compatibility → SEO-Friendly URLs*:
  - Enable SEO URLs (Yes/No, default **No**)
  - URL Prefix (default "parts" — use "fitment" / "for" / "compatibility"
    for different vibes; lowercase alphanumeric + dash only, anything
    else stripped, invalid values fall back to "parts")

#### 3. Customer "My Garage" widget
Customers save their vehicle for one-click reload across sessions.
Top-3 conversion driver in parts e-commerce per Amasty's own marketing.

- **New block**: `Block/Garage` — renders the widget when enabled.
- **New template**: `view/frontend/templates/garage/widget.phtml` —
  Alpine.js-driven, reads from `localStorage`, shows saved vehicles
  with one-click reload + individual remove + clear-all.
- **v1.1.0 MVP**: localStorage-based. Guest + logged-in customer get
  the same experience. v1.2.0+ will add customer attribute storage
  for logged-in users so the garage syncs across devices.
- **Merchant placement**: any layout XML reference or CMS block — the
  README documents the standard placement patterns (header, sidebar,
  hero, account page).
- **Auto-saves on Part Finder use**: the existing Alpine store
  `vehicleCompatSel` integrates with the garage automatically — no
  extra clicks for the customer.
- **Per-store-view scoped**: storage key includes the store ID so
  different stores don't share garages (different catalogs, different
  vehicle IDs).
- **Admin config** under *Stores → Configuration → eTechFlow → Vehicle
  Compatibility → Customer Garage*:
  - Enable Customer Garage (Yes/No, default **No**)
  - Maximum Vehicles per Customer (default **3** — clamped 1-10;
    sweet spot for "my car, my wife's car, my work van")

#### 4. Universal positioning
The `composer.json` description now leads with "Universal Product
Fitment Finder for Magento 2" instead of "Vehicle Compatibility".
The same code that already works for any fitment domain via the
v1.0.2 configurable labels is now positioned for it. Sells to:

- Automotive (still the primary)
- Motorcycle / marine / RV / ATV / bicycle parts (already worked)
- Phone cases (Make→Brand, hide Year, Earliest Year=2007)
- Watch straps (Brand/Watch/<hide year>/Strap Size)
- Printer cartridges, appliance parts, industrial fittings —
  anywhere the customer asks "will this fit my X?"

### Added (supporting infrastructure)

- `Model/Source/BadgeStyle.php` — source model for the PDP badge
  style dropdown (success / info / warning / neutral).
- Eight new `Config` getters: `isShowFitmentBadgeOnPdp()`,
  `getFitmentBadgePrefix()`, `getFitmentBadgeStyle()`,
  `isSeoUrlsEnabled()`, `getSeoUrlPrefix()`, `isSavedGarageEnabled()`,
  `getGarageMaxEntries()`, and the BADGE_STYLES whitelist for
  clamping.
- `Setup/Patch/Data/V110ReleaseMarker.php` — continues the always-a-
  patch discipline.

### Not changed

- **No schema changes** — drop-in upgrade from 1.0.3.
- **No breaking changes** — every new feature is opt-in (default off).
  Existing v1.0.x installs that don't touch the new config groups see
  zero behaviour change.
- **No API changes** — public block + service methods unchanged.

### Migration

```bash
composer require etechflow/module-vehicle-compat:^1.1.0
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

Pre-flight check:
```sql
SELECT module, schema_version, data_version FROM setup_module
WHERE module='ETechFlow_VehicleCompat';
```
Both should read `1.1.0`. If `data_version` is stale, re-run
`setup:upgrade` — do NOT flush cache yet.

To opt in to v1.1.0 features:
- **PDP badge**: *Stores → Configuration → eTechFlow → Vehicle
  Compatibility → PDP Fitment Badge → Enable* = Yes
- **SEO URLs**: *...SEO-Friendly URLs → Enable* = Yes (and decide on
  a prefix — "parts" is a safe default)
- **Garage**: *...Customer Garage → Enable* = Yes (and place the
  widget in your theme's layout XML or a CMS block)

### Competitive positioning

| Feature | Amasty PPF (~$399) | This module (v1.1.0) |
|---|---|---|
| Universal fitment | ✅ | ✅ (since v1.0.2) |
| Configurable labels | ✅ | ✅ (since v1.0.2) |
| Multi-axis (2-5 levels) | ✅ | ⚠️ Fixed 4 axes (Make/Model/Year/Part) |
| PDP fitment badge | ✅ | ✅ |
| SEO URLs | ✅ | ✅ |
| Saved garage | ✅ | ✅ (localStorage MVP) |
| Customer-attribute garage sync | ✅ | v1.2.0 |
| CSV import | ✅ | ✅ |
| OEM/part-number search | ✅ Pro | v1.3.0 |
| Multiple finders per store | ✅ | v1.3.0 |

Credible alternative at a fraction of the price.

---

## [1.0.3] — 2026-06-03 — Restore docs accidentally pruned during v1.0.2 publish-repo sync

The v1.0.2 release shipped clean code but the publish-repo rsync
accidentally deleted the top-level documentation files
(INSTALL.md, USAGE.md, CONFIGURATION.md, COMPATIBILITY.md,
UNINSTALL.md) that ship at the repo root alongside README and
CHANGELOG. This release restores them.

No code change. No behaviour change. Pure documentation file
restoration plus V103ReleaseMarker for always-a-patch discipline.

If you installed 1.0.2 you're functionally fine — composer doesn't
care about INSTALL.md vs not. But the GitHub repo page was missing
those docs and 1.0.3 puts them back.

### Migration

```bash
composer require etechflow/module-vehicle-compat:^1.0.3
bin/magento setup:upgrade
bin/magento cache:flush
```

---

## [1.0.2] — 2026-06-03 — Universal fitment: admin-configurable labels, Year bounds, optional Year field

Same module, three new admin knobs that make it work for any
product-fitment domain — not just vehicles. Drop-in upgrade from
1.0.1, no schema change, no breaking change. Default behaviour
identical to 1.0.1 (Year field visible, "Make/Model/Year/Parts"
labels, year range 1990 – current).

### Added

Three new admin fields under *Stores → Configuration → eTechFlow →
Vehicle Compatibility → General Settings*:

1. **Earliest Year** — text field, default `1990`. The oldest year
   that appears in the Year dropdown. Set to `1950` for vintage car
   parts shops (classic Mustangs, Series Land Rovers). Set to `2007`
   for smartphone-fitment shops where there's no point listing
   pre-iPhone years. Anything below 1900 or above the current year
   gets clamped to safe bounds.

2. **Show Year Field** — Yes/No, default Yes. When No, the Year
   dropdown disappears from the Part Finder form. The form becomes
   Make → Model → Parts, which is what phone case shops, watch strap
   shops, printer cartridge shops, and appliance parts shops actually
   need.

3. **Field Labels** — 4 separate text fields for the customer-facing
   labels:
   - **Make Field Label** (default "Make") — set to "Brand" for
     phone cases / watches / appliances
   - **Model Field Label** (default "Model") — set to "Phone" /
     "Watch" / "Appliance Model"
   - **Year Field Label** (default "Year") — set to "Generation"
     for phones, "Year of Manufacture" for older fitments
   - **Parts Field Label** (default "Parts Required") — set to
     "Type" / "Style" / "Strap Size" / "Component"

   When the labels are configured, the Part Finder dropdowns render
   the merchant's wording instead of "Select Make / Select Model /
   etc." A blank label falls back to the default.

### Changed

- **`Model/Source/Year.php`** — `MIN_YEAR` constant is now
  `@deprecated`; the year source reads from `Config::getEarliestYear()`
  instead. Constant kept on disk so any third-party code referencing
  it doesn't immediately fatal.

- **`Block/PartFinderData.php`** — gains 5 public getters:
  `getMakeLabel()`, `getModelLabel()`, `getYearLabel()`,
  `getPartLabel()`, `isYearFieldEnabled()`. Templates and any
  custom integration can read the configured values.

- **`view/frontend/templates/partfinder/form.phtml`** — uses the
  configured labels for placeholder texts; wraps the Year field
  block with `if ($block->isYearFieldEnabled())` so it can disappear
  entirely when not relevant.

- **`Setup/Patch/Data/V102ReleaseMarker.php`** — no-op release
  marker patch, depends on V101.

### Why this matters

This release transforms the module from "Vehicle Compatibility" into
a "Universal Product Fitment Finder". The same code now sells to:

- **Auto parts** (as before — `Make/Model/Year/Parts` works perfectly)
- **Motorcycle / marine / RV / ATV / bicycle parts** (same labels work)
- **Vintage car parts** (set Earliest Year to 1950)
- **Phone cases** (set labels to `Brand/Phone/Generation/Style`, hide
  Year, set Earliest Year to 2007)
- **Watch straps** (`Brand/Watch/<hide year>/Strap Size`)
- **Printer cartridges** (`Brand/Printer/Year/Cartridge Type`)
- **Appliance parts** (`Brand/Appliance Model/Year/Part Type`)
- **Any product fitment problem** the merchant can map to 2-4 axes

Competing against Amasty Product Parts Finder (~$399) at a fraction
of the price.

### Not changed

- No schema changes — drop-in upgrade from 1.0.1
- No URL changes — existing `/vehiclecompat/find/index` URLs keep working
- No API changes — public block + service methods unchanged
- Default behaviour identical to 1.0.1 — merchants who don't touch
  the new fields see no change at all

### Migration

```bash
composer require etechflow/module-vehicle-compat:^1.0.2
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

Pre-flight check:
```sql
SELECT module, schema_version, data_version FROM setup_module
WHERE module='ETechFlow_VehicleCompat';
```
Both should read `1.0.2`. If `data_version` is stale, re-run
`setup:upgrade` — do NOT flush cache yet.

To opt in to the universal-fitment positioning:
1. Set your merchant's label preferences in
   *Stores → Configuration → eTechFlow → Vehicle Compatibility →
   General Settings*.
2. If your fitment domain doesn't use year, set Show Year Field to No.
3. Save → flush cache → reload the Part Finder.

---

## [1.0.1] — 2026-06-03 — Brand de-leak: rename Keystation-derived routes, files, and CSS classes

Cosmetic but important release. Renames every customer-visible and
admin-visible identifier that still carried the original developer's
"Keystation Vehicle Compatibility" (kvc) / "Keystation" branding, so the
module ships as a generic eTechFlow product any merchant can install
without seeing another shop's name in their URLs or DevTools.

### Changed (customer-facing)

- **URL prefix renamed**: `frontName="kvc"` → `frontName="vehiclecompat"`.
  Part Finder page is now at `/vehiclecompat/find/index` instead of
  `/kvc/find/index`. The Options + Tree AJAX endpoints follow:
  `/vehiclecompat/options/index`, `/vehiclecompat/tree.json`.
- **Frontend JS file renamed**: `view/frontend/web/js/kvc-part-finder.js`
  → `part-finder.js`. The Alpine.js function inside is now
  `vehicleCompatPartFinder()` (was `kvcPartFinder()`), and its store key
  is `'vehicleCompatSel'` (was `'kvcSel'`).
- **CSS class prefix**: `kvc-*` → `vc-*` across all templates (`vc-row`,
  `vc-ico-left`, `vc-trigger`, `vc-side`, `vc-find-page`, `vc-pager`,
  `vc-cat-chips`, etc.). Keeps the prefix short while removing the
  Keystation branding.
- **Frontend layout file**: `view/frontend/layout/kvc_find_index.xml` →
  `vehiclecompat_find_index.xml`.
- **Block names** in layout XML: `kvc.sidebar.summary` /
  `kvc.category.filter.chips` → `vehiclecompat.sidebar.summary` /
  `vehiclecompat.category.filter.chips`.

### Changed (admin-facing)

- **11 admin layout + UI component files** renamed from
  `keystation_vehicle_*` to `etechflow_vehicle_*` so they match the
  module's existing admin route id (`etechflow_vehicle`). Previously
  they were dead-code on disk (route id and file name didn't match;
  Magento auto-loads layout by URL pattern). Renaming gets them back
  on the auto-load path under the canonical eTechFlow naming.

### Added

- **`Setup/Patch/Data/V101ReleaseMarker.php`** — no-op release marker
  patch. Continues the always-a-patch discipline. Depends on the three
  v1.0.0 data patches so patches run in version order.

### Breaking changes ⚠

Anyone who installed v1.0.0 in the ~1 hour between v1.0.0 and v1.0.1
publication will see the Part Finder URL change. No real customers
were installed at the time of this release. Bookmarks pointing at
`/kvc/find/index` will 404 — clients should update to
`/vehiclecompat/find/index`.

If you've embedded the Part Finder form in CMS blocks or themes via
JavaScript, the Alpine.js function call needs renaming:
`kvcPartFinder()` → `vehicleCompatPartFinder()`. Same for any
`Alpine.store('kvcSel')` references.

### Why this exists

The original developer of this module built it first for the Keystation
brand and then handed the code to eTechFlow. The brand prefixes
(`kvc/`, `keystation_vehicle_*`) survived the rebadge. v1.0.0 shipped
with that leak. v1.0.1 cleans it up so the module sells generically.

### Migration

```bash
composer require etechflow/module-vehicle-compat:^1.0.1
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

Pre-flight check after upgrade:
```sql
SELECT module, schema_version, data_version FROM setup_module
WHERE module='ETechFlow_VehicleCompat';
```
Both columns should read `1.0.1`. If `data_version` is stale, re-run
`setup:upgrade` — do NOT flush cache yet.

After upgrade, the Part Finder page is at `/vehiclecompat/find/index`.
A merchant who wants to preserve the old `/kvc/*` URLs can ship a
custom URL rewrite from `/kvc/*` to `/vehiclecompat/*` in their web
server config — but a fresh install no longer publishes any `/kvc/*`
URLs at all.

---

## [1.0.0] — 2026-05-20

First public release as a standalone, theme-agnostic Magento 2 module.

### Added

- **Vehicle compatibility data**
  - `vehicle_compat_data` product attribute (JSON) — Make / Model / Year tuples per product
  - `parts_required` multi-select product attribute
  - Admin Makes CRUD under Catalog → Vehicles → Makes
  - Admin Models CRUD under Catalog → Vehicles → Models
  - Product editor tab with visual Make/Model/Year picker (no hand-edited JSON)
  - CSV import command: `bin/magento etechflow:vehiclecompat:import-parts`
- **Part Finder widget**
  - Reusable form fragment (`ETechFlow_VehicleCompat::partfinder/form.phtml`) — embed anywhere
  - Server-side filtered options endpoint `/vehiclecompat/options/index` (bidirectional)
  - Full vehicle tree endpoint `/vehiclecompat/tree/index` (cached, browser-cacheable)
  - Find-parts results page `/vehiclecompat/find/index` with category chips
  - Shared Alpine store keeps multiple form instances in sync (header modal + hero + sidebar)
- **Theme-agnostic JS bootstrap**
  - `alpine-bootstrap.js` detects Alpine, lazy-loads it from CDN if absent
  - `part-finder.js` factory function — loaded once via layout XML
  - Both loaded on every storefront page via `view/frontend/layout/default.xml`
- **Scoped namespaced CSS**
  - `.vc-*` class prefix prevents theme collisions
  - Inline `<style>` block in `partfinder/styles.phtml`
- **Catalog filter integration**
  - `Plugin\Catalog\Layer\FilterByVehicle` narrows product collections by `?make_id=&model_id=&year=&part_id=` URL params
  - `Plugin\Catalog\Block\HideLayeredNav` hides the layered nav on `/vehiclecompat/find/index` pages
- **Documentation bundle**
  - README, INSTALL, USAGE, CONFIGURATION, COMPATIBILITY, CHANGELOG, UNINSTALL, LICENSE

### Compatibility

- Magento 2.4.4 – 2.4.8
- PHP 8.1, 8.2, 8.3
- Hyvä Theme (native — Alpine global)
- Luma / Blank / custom themes (Alpine auto-loaded from CDN)
- Adobe Commerce + Magento Open Source + Mage-OS

---

[1.0.0]: https://github.com/etechflow/module-vehicle-compat/releases/tag/v1.0.0
