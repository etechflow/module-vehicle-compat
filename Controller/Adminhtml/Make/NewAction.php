<?php
declare(strict_types=1);

namespace ETechFlow\VehicleCompat\Controller\Adminhtml\Make;

use Magento\Backend\App\Action;

class NewAction extends Action
{
    public const ADMIN_RESOURCE = 'ETechFlow_VehicleCompat::make';

    public function execute()
    {
        $resultForward = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('edit');
    }
}
