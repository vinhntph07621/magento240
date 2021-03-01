<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-24
 * Time: 16:29
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Location\Set;

class Add extends \Omnyfy\Vendor\Controller\Adminhtml\Location\Set
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $pageFactory
    )
    {
        parent::__construct($context, $coreRegistry);
        $this->resultPageFactory = $pageFactory;
    }

    public function execute()
    {
        $this->_setTypeId();

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Vendor::location_sets');
        $resultPage->getConfig()->getTitle()->prepend(_('New Attribute Set'));
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Omnyfy\Vendor\Block\Adminhtml\Location\Attribute\Set\Toolbar\Add')
        );
        return $resultPage;
    }
}
 