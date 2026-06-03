<?php
declare(strict_types=1);

namespace ETechFlow\VehicleCompat\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Year implements OptionSourceInterface
{
    public const MIN_YEAR = 1990;

    public function toOptionArray(): array
    {
        $options = [];
        $max = (int)date('Y') + 1;
        for ($y = $max; $y >= self::MIN_YEAR; $y--) {
            $options[] = ['value' => $y, 'label' => (string)$y];
        }
        return $options;
    }
}
