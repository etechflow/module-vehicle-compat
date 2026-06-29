<?php

declare(strict_types=1);

namespace ETechFlow\VehicleCompat\Cron;

use ETechFlow\VehicleCompat\Model\LicenseValidator;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\CacheInterface;

/**
 * Periodically re-checks the licence with the portal and flushes the storefront
 * cache when the enforced state changes, so portal suspensions / IP removals
 * take effect without waiting for the FPC to expire.
 */
class RevalidateLicense
{
    private const STATE_CACHE_KEY = 'etf_vehiclecompat_license_enforced_state';

    public function __construct(
        private readonly LicenseValidator $licenseValidator,
        private readonly CacheInterface $cache,
        private readonly TypeListInterface $cacheTypeList
    ) {
    }

    public function execute(): void
    {
        $this->cache->clean([LicenseValidator::CACHE_TAG]);

        $now  = $this->licenseValidator->isValid() ? '1' : '0';
        $last = $this->cache->load(self::STATE_CACHE_KEY);

        if ($last !== false && (string) $last === $now) {
            return;
        }

        $this->cacheTypeList->cleanType('full_page');
        $this->cacheTypeList->cleanType('block_html');

        $this->cache->save($now, self::STATE_CACHE_KEY);
    }
}
