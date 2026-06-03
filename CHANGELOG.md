# Changelog — ETechFlow_VehicleCompat

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
