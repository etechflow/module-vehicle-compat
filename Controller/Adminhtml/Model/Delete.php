<?php
declare(strict_types=1);

namespace ETechFlow\VehicleCompat\Controller\Adminhtml\Model;

use ETechFlow\VehicleCompat\Model\ModelFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'ETechFlow_VehicleCompat::model';

    private ModelFactory $modelFactory;

    public function __construct(Context $context, ModelFactory $modelFactory)
    {
        parent::__construct($context);
        $this->modelFactory = $modelFactory;
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('model_id');
        if ($id) {
            try {
                $model = $this->modelFactory->create();
                $model->load($id);
                if ($model->getId()) {
                    $model->delete();
                    $this->messageManager->addSuccessMessage(__('Model deleted.'));
                }
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $redirect->setPath('*/*/');
    }
}
