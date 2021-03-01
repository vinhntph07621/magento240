<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-16
 * Time: 15:35
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Set;

class Add extends \Omnyfy\Vendor\Controller\Adminhtml\Vendor\Set
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $this->_setTypeId();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Vendor::vendor_sets');
        $resultPage->getConfig()->getTitle()->prepend(__('New Attribute Set'));
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Omnyfy\Vendor\Block\Adminhtml\Vendor\Attribute\Set\Toolbar\Add')
        );
        return $resultPage;
    }
}
 