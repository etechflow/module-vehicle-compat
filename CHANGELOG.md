# Changelog — ETechFlow_VehicleCompat

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
