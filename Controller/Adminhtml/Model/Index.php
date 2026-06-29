<?php
declare(strict_types=1);

namespace ETechFlow\VehicleCompat\Controller\Adminhtml\Model;

use ETechFlow\VehicleCompat\Model\LicenseValidator;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'ETechFlow_VehicleCompat::model';

    private PageFactory $resultPageFactory;
    private LicenseValidator $licenseValidator;

    public function __construct(Context $context, PageFactory $resultPageFactory, LicenseValidator $licenseValidator)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->licenseValidator = $licenseValidator;
    }

    public function execute()
    {
        if (!$this->licenseValidator->isValid()) {
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('etechflow_vehicle/license/gate');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('ETechFlow_VehicleCompat::model');
        $resultPage->getConfig()->getTitle()->prepend(__('Vehicle Models'));
        return $resultPage;
    }
}
