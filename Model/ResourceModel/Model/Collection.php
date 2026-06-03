<?php
declare(strict_types=1);

namespace ETechFlow\VehicleCompat\Model\ResourceModel\Model;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'model_id';
    protected $_eventPrefix = 'etechflow_vehicle_model_collection';
    protected $_eventObject = 'model_collection';

    protected function _construct()
    {
        $this->_init(
            \ETechFlow\VehicleCompat\Model\Model::class,
            \ETechFlow\VehicleCompat\Model\ResourceModel\Model::class
        );
    }
}
