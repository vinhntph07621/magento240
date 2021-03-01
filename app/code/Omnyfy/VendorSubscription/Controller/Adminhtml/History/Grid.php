<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-11
 * Time: 11:46
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\History;

class Grid extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::histories';

    protected $layoutFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Result\LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context, $resultPageFactory, $logger);
    }

    public function execute()
    {
        $result = $this->layoutFactory->create();
        $result->getLayout()->getBlock('omnyfy_vendor.edit.tab.history');
        return $result;
    }
}
 