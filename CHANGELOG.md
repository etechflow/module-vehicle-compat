# Changelog — ETechFlow_VehicleCompat

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
  - Server-side filtered options endpoint `/kvc/options/index` (bidirectional)
  - Full vehicle tree endpoint `/kvc/tree/index` (cached, browser-cacheable)
  - Find-parts results page `/kvc/find/index` with category chips
  - Shared Alpine store keeps multiple form instances in sync (header modal + hero + sidebar)
- **Theme-agnostic JS bootstrap**
  - `alpine-bootstrap.js` detects Alpine, lazy-loads it from CDN if absent
  - `kvc-part-finder.js` factory function — loaded once via layout XML
  - Both loaded on every storefront page via `view/frontend/layout/default.xml`
- **Scoped namespaced CSS**
  - `.kvc-*` class prefix prevents theme collisions
  - Inline `<style>` block in `partfinder/styles.phtml`
- **Catalog filter integration**
  - `Plugin\Catalog\Layer\FilterByVehicle` narrows product collections by `?make_id=&model_id=&year=&part_id=` URL params
  - `Plugin\Catalog\Block\HideLayeredNav` hides the layered nav on `/kvc/find/index` pages
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
