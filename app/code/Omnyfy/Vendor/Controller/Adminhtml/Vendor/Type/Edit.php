<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-09
 * Time: 10:59
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Type;

use Omnyfy\Vendor\Controller\Adminhtml\AbstractAction;

class Edit extends AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendor_types';

    protected $resourceKey = 'Omnyfy_Vendor::vendor_types';

    protected $adminTitle = 'Vendor Type';

    protected $_vendorTypeFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Vendor\Model\VendorTypeFactory $vendorTypeFactory
    )
    {
        $this->_vendorTypeFactory = $vendorTypeFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_vendorTypeFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage('Vendor Type not exists.');
                $this->_redirect('omnyfy_vendor/*');
                return;
            }
        }

        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_omnyfy_vendor_vendor_type', $model);

        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('Omnyfy_Vendor::vendor_types');
        $resultPage->getConfig()->getTitle()->prepend(__('Vendor Type'));
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend($model->getTypeName());
        }
        else{
            $resultPage->getConfig()->getTitle()->prepend(__('New Vendor Type'));
        }

        $resultPage->getLayout()->getBlock('omnyfy_vendor_vendor_type_edit');

        return $resultPage;
    }
}
 