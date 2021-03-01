<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 4/2/18
 * Time: 11:31 PM
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Inventory;


class MassDelete extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::inventory';

    protected $filter;

    protected $collectionFactory;

    protected $locationRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Omnyfy\Vendor\Model\Resource\Inventory\CollectionFactory $collectionFactory,
        \Omnyfy\Vendor\Api\LocationRepositoryInterface $locationRepository
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->locationRepository = $locationRepository;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }


    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $locationId = $this->_session->getCurrentLocationId();
        $collection->addFieldToFilter('location_id', $locationId);

        $productDeleted = 0;
        foreach ($collection->getItems() as $inventory) {
            $inventory->delete();
            $productDeleted++;
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $productDeleted)
        );
        
        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('omnyfy_vendor/location/stock', ['id' => $locationId]);
    }
}