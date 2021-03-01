<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-15
 * Time: 16:06
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Store;

use Omnyfy\Vendor\Controller\Adminhtml\AbstractAction;

class Edit extends AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendor_stores';

    protected $resourceKey = 'Omnyfy_Vendor::vendor_stores';

    protected $adminTitle = 'Vendors';

    protected $vendorFactory;

    protected $vendorTypeFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Omnyfy\Vendor\Model\VendorTypeFactory $vendorTypeFactory

    )
    {
        $this->vendorFactory = $vendorFactory;
        $this->vendorTypeFactory = $vendorTypeFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $vendor = $this->loadVendor($this->getRequest());

        if ($id && !$vendor->getEntityId()) {
            $this->messageManager->addErrorMessage(__('This vendor no longer exists.'));
            return $this->resultRedirectFactory->create()->setPath('omnyfy_vendor/*');
        }

        $this->_eventManager->dispatch('omnyfy_vendor_vendor_store_edit_action', ['vendor' => $vendor]);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Vendor::vendor_stores');
        $resultPage->getConfig()->getTitle()->prepend(__('Vendors'));
        $resultPage->getConfig()->getTitle()->prepend($vendor->getName());

        return $resultPage;
    }

    protected function loadVendor($request)
    {
        $vendorId = intval($request->getParam('id'));

        $vendor = $this->vendorFactory->create();
        if ($vendorId) {
            try {
                $vendor->load($vendorId);
            }
            catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }

        $typeId = intval($request->getParam('type_id'));
        if (!$vendorId && $typeId) {
            //load type by typeId, set type_id and attribute_set_id
            $vendorType = $this->vendorTypeFactory->create();
            $vendorType->load($typeId);

            if ($vendorType->getTypeId() > 0 && $typeId == $vendorType->getTypeId()) {
                $vendor->setTypeId($typeId);
                $vendor->setAttributeSetId($vendorType->getVendorAttributeSetId());
            }
        }

        $this->_coreRegistry->register('current_omnyfy_vendor_store', $vendor);
        return $vendor;
    }
}
 