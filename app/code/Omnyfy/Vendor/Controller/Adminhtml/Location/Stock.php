<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 25/1/18
 * Time: 9:37 AM
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Location;

class Stock extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::locations';

    protected $resourceKey = 'Omnyfy_Vendor::locations';

    protected $adminTitle = 'Inventory';

    protected $locationFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Vendor\Model\LocationFactory $locationFactory
    )
    {
        $this->locationFactory = $locationFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        //TODO: check location id, redirect to 404 if not found

        $id = $this->getRequest()->getParam('id');
        $model = $this->locationFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId() || $id != $model->getId()) {
                $this->messageManager->addError(__('This location no longer exists.'));
                $this->_redirect('omnyfy_vendor/*');
                return;
            }
        }

        $this->_session->setCurrentLocationId($id);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Inventory'));
        $resultPage->addBreadcrumb(__('Omnyfy'), __('Omnyfy'));
        $resultPage->addBreadcrumb(__('Inventory'), __('Inventory'));
        return $resultPage;
    }

}