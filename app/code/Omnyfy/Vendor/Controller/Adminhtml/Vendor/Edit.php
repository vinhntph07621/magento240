<?php
/**
 * Copyright © 2017 Omnyfy. All rights reserved.
 */

namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor;

use Magento\Backend\App\Action;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Omnyfy\Vendor\Model\VendorFactory;
use Psr\Log\LoggerInterface;

class Edit extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendors';
    protected $resourceKey = 'Omnyfy_Vendor::vendors';

    protected $adminTitle = 'Vendors';

    protected $vendorFactory;

    public function __construct(
        VendorFactory $vendorFactory,
        Action\Context $context,
        Registry $coreRegistry,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        Session $authSession,
        LoggerInterface $logger)
    {

        $this->vendorFactory = $vendorFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->vendorFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This vendor no longer exists.'));
                $this->_redirect('omnyfy_vendor/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $this->_coreRegistry->register('current_omnyfy_vendor_vendor', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('vendor_vendor_edit');
        $this->_view->renderLayout();
    }
}
