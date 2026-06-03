<?php
declare(strict_types=1);

namespace ETechFlow\VehicleCompat\Controller\Adminhtml\Model;

use Magento\Backend\App\Action;

class NewAction extends Action
{
    public const ADMIN_RESOURCE = 'ETechFlow_VehicleCompat::model';

    public function execute()
    {
        $resultForward = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_FORWARD);
        return $resultForward->forward('edit');
    }
}
