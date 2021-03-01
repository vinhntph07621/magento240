<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-24
 * Time: 16:51
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Location\Set;

class Index extends \Omnyfy\Vendor\Controller\Adminhtml\Location\Set
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        parent::__construct($context, $coreRegistry);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $this->_setTypeId();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Vendor::location_sets');
        $resultPage->getConfig()->getTitle()->prepend(__('Location Attribute Sets'));
        $resultPage->addBreadcrumb(__('Location'), __('Location'));
        $resultPage->addBreadcrumb(__('Manage Attribute Sets'), __('Attribute Sets'));
        return $resultPage;
    }
}

 