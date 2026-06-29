<?php

declare(strict_types=1);

namespace ETechFlow\VehicleCompat\Controller\Adminhtml\License;

use ETechFlow\VehicleCompat\Model\LicenseValidator;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Admin License Gate page. If licensed, redirect to the module config page;
 * otherwise render the gate (plan cards + broker checkout form).
 */
class Gate extends Action
{
    public const ADMIN_RESOURCE = 'ETechFlow_VehicleCompat::config';

    public function __construct(
        Context $context,
        private readonly LicenseValidator $licenseValidator,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        if ($this->licenseValidator->isValid()) {
            $this->messageManager->addSuccessMessage(
                (string) __('Vehicle Compatibility is licensed. Configure the module below.')
            );
            return $this->resultRedirectFactory->create()->setPath(
                'adminhtml/system_config/edit',
                ['section' => 'etechflow_vehiclecompat']
            );
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((string) __('Vehicle Compatibility - License Required'));
        return $resultPage;
    }
}
