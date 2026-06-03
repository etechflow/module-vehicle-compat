<?php
declare(strict_types=1);

namespace ETechFlow\VehicleCompat\Controller\Adminhtml\Make;

use ETechFlow\VehicleCompat\Model\MakeFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'ETechFlow_VehicleCompat::make';

    private MakeFactory $makeFactory;

    public function __construct(Context $context, MakeFactory $makeFactory)
    {
        parent::__construct($context);
        $this->makeFactory = $makeFactory;
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('make_id');
        if ($id) {
            try {
                $model = $this->makeFactory->create();
                $model->load($id);
                if ($model->getId()) {
                    $model->delete();
                    $this->messageManager->addSuccessMessage(__('Make deleted.'));
                }
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $redirect->setPath('*/*/');
    }
}
