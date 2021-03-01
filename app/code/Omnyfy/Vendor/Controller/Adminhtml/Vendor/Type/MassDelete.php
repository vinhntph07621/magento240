<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-25
 * Time: 11:10
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Type;

class MassDelete extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendor_types';

    protected $resourceKey = 'Omnyfy_Vendor::vendor_types';

    protected $filter;

    protected $collectionFactory;

    protected $vendorResource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Omnyfy\Vendor\Model\Resource\VendorType\CollectionFactory $collectionFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->vendorResource = $vendorResource;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }


    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $vendorTypeDeleted = 0;
        foreach ($collection->getItems() as $vendorType) {
            if (1 == $vendorType->getId() || 1 == $vendorType->getTypeId()) {
                $this->messageManager->addErrorMessage('Default type could not been removed.');
                continue;
            }

            //TODO: change all vendor with this type to default
            $this->vendorResource->updateLocationVendorTypeIdToDefault($vendorType->getId(), true);
            $this->vendorResource->updateVendorTypeIdToDefault($vendorType->getId(), true);

            $vendorType->delete();
            $vendorTypeDeleted++;
        }
        $this->messageManager->addSuccessMessage(
            'A total of ' . $vendorTypeDeleted . ' record(s) have been deleted.'
        );

        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('omnyfy_vendor/vendor_type/index');
    }
}
 