<?php

declare(strict_types=1);

namespace ETechFlow\VehicleCompat\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Typed config reader for the VehicleCompat module.
 *
 * v1.0.2 added the "Universal Fitment" options group: configurable Year
 * lower bound, optional Year field, and admin-customisable labels for
 * Make / Model / Year / Part so the same module can sell to vehicle,
 * cycling, marine, RV, and non-vehicle product-fitment domains without
 * code changes.
 */
class Config
{
    private const XML_PATH_ENABLED              = 'etechflow_vehiclecompat/general/enabled';
    private const XML_PATH_EARLIEST_YEAR        = 'etechflow_vehiclecompat/general/earliest_year';
    private const XML_PATH_SHOW_YEAR_FIELD      = 'etechflow_vehiclecompat/general/show_year_field';
    private const XML_PATH_LABEL_MAKE           = 'etechflow_vehiclecompat/general/label_make';
    private const XML_PATH_LABEL_MODEL          = 'etechflow_vehiclecompat/general/label_model';
    private const XML_PATH_LABEL_YEAR           = 'etechflow_vehiclecompat/general/label_year';
    private const XML_PATH_LABEL_PART           = 'etechflow_vehiclecompat/general/label_part';

    // v1.1.0 — PDP fitment badge
    private const XML_PATH_SHOW_PDP_BADGE       = 'etechflow_vehiclecompat/pdp_badge/enabled';
    private const XML_PATH_PDP_BADGE_PREFIX     = 'etechflow_vehiclecompat/pdp_badge/prefix';
    private const XML_PATH_PDP_BADGE_STYLE      = 'etechflow_vehiclecompat/pdp_badge/style';

    // v1.1.0 — SEO URLs
    private const XML_PATH_SEO_URLS_ENABLED     = 'etechflow_vehiclecompat/seo_urls/enabled';
    private const XML_PATH_SEO_URL_PREFIX       = 'etechflow_vehiclecompat/seo_urls/prefix';

    // v1.1.0 — Saved garage
    private const XML_PATH_GARAGE_ENABLED       = 'etechflow_vehiclecompat/garage/enabled';
    private const XML_PATH_GARAGE_MAX_ENTRIES   = 'etechflow_vehiclecompat/garage/max_entries';

    /** Allowed badge style modifiers — clamped against this whitelist. */
    private const BADGE_STYLES = ['success', 'info', 'warning', 'neutral'];

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Lower bound for the Year dropdown. Default 1990. Merchant can set
     * e.g. 1950 for vintage car parts shops, 2007 for smartphone-fitment
     * shops, etc. Anything below 1900 or above current year is clamped
     * to sensible bounds.
     */
    public function getEarliestYear(?int $storeId = null): int
    {
        $value = (int) $this->scopeConfig->getValue(
            self::XML_PATH_EARLIEST_YEAR,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($value < 1900) {
            return 1990;
        }
        $currentYear = (int) date('Y');
        if ($value > $currentYear) {
            return $currentYear;
        }
        return $value;
    }

    /**
     * Should the Year field render in the Part Finder form? Default Yes.
     * Set No for fitment domains that don't have a year axis (phone cases,
     * watch straps, printer cartridges, appliance parts, etc.)
     */
    public function isYearFieldEnabled(?int $storeId = null): bool
    {
        // isSetFlag returns true when the config value is "1" / "yes" / etc.
        // Default in config.xml is "1" so a fresh install gets Yes.
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_YEAR_FIELD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getMakeLabel(?int $storeId = null): string
    {
        return $this->labelOrDefault(self::XML_PATH_LABEL_MAKE, 'Make', $storeId);
    }

    public function getModelLabel(?int $storeId = null): string
    {
        return $this->labelOrDefault(self::XML_PATH_LABEL_MODEL, 'Model', $storeId);
    }

    public function getYearLabel(?int $storeId = null): string
    {
        return $this->labelOrDefault(self::XML_PATH_LABEL_YEAR, 'Year', $storeId);
    }

    public function getPartLabel(?int $storeId = null): string
    {
        return $this->labelOrDefault(self::XML_PATH_LABEL_PART, 'Parts Required', $storeId);
    }

    private function labelOrDefault(string $path, string $default, ?int $storeId): string
    {
        $value = (string) $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
        $value = trim($value);
        return $value !== '' ? $value : $default;
    }

    /** v1.1.0 — should the PDP "This fits:" badge render? Default off. */
    public function isShowFitmentBadgeOnPdp(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_PDP_BADGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getFitmentBadgePrefix(?int $storeId = null): string
    {
        return $this->labelOrDefault(self::XML_PATH_PDP_BADGE_PREFIX, 'Fits:', $storeId);
    }

    /** Style modifier — clamped to BADGE_STYLES whitelist. */
    public function getFitmentBadgeStyle(?int $storeId = null): string
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_PDP_BADGE_STYLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return in_array($value, self::BADGE_STYLES, true) ? $value : 'success';
    }

    /** v1.1.0 — should SEO-friendly URLs route Part Finder requests? Default off. */
    public function isSeoUrlsEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SEO_URLS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * URL prefix for SEO routes. Default "parts" → /parts/bmw/3-series/2020/brake-pads.
     * Sanitised to lowercase alphanumeric + dash; empty or unsafe values
     * fall back to "parts".
     */
    public function getSeoUrlPrefix(?int $storeId = null): string
    {
        $value = (string) $this->scopeConfig->getValue(
            self::XML_PATH_SEO_URL_PREFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-]/', '', $value) ?: '';
        return $value !== '' ? $value : 'parts';
    }

    /** v1.1.0 — should the My Garage widget render when placed? Default off. */
    public function isSavedGarageEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GARAGE_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /** Max vehicles a customer can save. Default 3. Clamped to 1-10. */
    public function getGarageMaxEntries(?int $storeId = null): int
    {
        $value = (int) $this->scopeConfig->getValue(
            self::XML_PATH_GARAGE_MAX_ENTRIES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($value < 1) { return 3; }
        if ($value > 10) { return 10; }
        return $value;
    }
}
