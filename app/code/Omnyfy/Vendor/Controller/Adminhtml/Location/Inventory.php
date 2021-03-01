<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/8/17
 * Time: 6:46 PM
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Location;

class Inventory extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::locations';

    protected $resourceKey = 'Omnyfy_Vendor::locations';

    protected $adminTitle = 'Location';

    protected $productBuilder;

    protected $resultLayoutFactory;

    public function __construct(
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger)
    {
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->productBuilder = $productBuilder;

        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {

        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('omnyfy_vendor_location_edit_tab_inventory')
            ->setProducts($this->getRequest()->getPost('products', null));
        return $resultLayout;

    }
}