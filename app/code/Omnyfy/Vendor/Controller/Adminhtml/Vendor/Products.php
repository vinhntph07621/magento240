<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 18/7/17
 * Time: 9:05 PM
 */

namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor;

class Products extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendors';
    protected $resourceKey = 'Omnyfy_Vendor::vendors';

    protected $adminTitle = 'Vendors';

    protected $layoutFactory;

    public function __construct(
        \Magento\Framework\View\Result\LayoutFactory $layoutFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        $result = $this->layoutFactory->create();
        $result->getLayout()->getBlock('omnyfy_vendor.edit.tab.products')
            ->setInProducts($this->getRequest()->getParam('vendor_products', null));

        return $result;
    }
}