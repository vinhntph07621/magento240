<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/8/17
 * Time: 10:17 AM
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Location;


class Edit extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::locations';

    protected $resourceKey = 'Omnyfy_Vendor::locations';

    protected $adminTitle = 'Location';

    protected $locationFactory;

    public function __construct(
        \Omnyfy\Vendor\Model\LocationFactory $locationFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger)
    {
        $this->locationFactory = $locationFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->locationFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This location no longer exists.'));
                $this->_redirect('omnyfy_vendor/*');
                return;
            }
        }
        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $vendorInfo = $this->_session->getVendorInfo();
        if (!empty($vendorInfo)) {
            $model->setVendorId($vendorInfo['vendor_id']);
        }
        $country = $model->getData('country');
        if (empty($country)) {
            $model->setData('country', 'AU');
        }
        $this->_coreRegistry->register('current_omnyfy_vendor_location', $model);
        $this->_initAction();
        $this->_view->getLayout()->getBlock('omnyfy_vendor_location_edit');
        $this->_view->renderLayout();
    }
}