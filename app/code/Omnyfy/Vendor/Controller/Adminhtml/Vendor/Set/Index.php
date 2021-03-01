<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-11
 * Time: 15:57
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Set;

class Index extends \Omnyfy\Vendor\Controller\Adminhtml\Vendor\Set
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
        $resultPage->setActiveMenu('Omnyfy_Vendor::vendor_sets');
        $resultPage->getConfig()->getTitle()->prepend(__('Vendor Attribute Sets'));
        $resultPage->addBreadcrumb(__('Vendor'), __('Vendor'));
        $resultPage->addBreadcrumb(__('Manage Attribute Sets'), __('Attribute Sets'));
        return $resultPage;
    }
}
 