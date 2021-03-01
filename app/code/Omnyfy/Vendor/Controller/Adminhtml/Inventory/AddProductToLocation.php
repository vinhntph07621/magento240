<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 3/2/18
 * Time: 11:09 AM
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Inventory;

use Magento\Framework\Exception\LocalizedException;
class AddProductToLocation extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::inventory';

    protected $resultJsonFactory;

    protected $inventoryResource;

    protected $locationRepository;

    protected $vendorResource;

    protected $config;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Omnyfy\Vendor\Model\Resource\Inventory $inventoryResource,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Omnyfy\Vendor\Api\LocationRepositoryInterface $locationRepository,
        \Omnyfy\Vendor\Model\Config $config
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->inventoryResource = $inventoryResource;
        $this->vendorResource = $vendorResource;
        $this->locationRepository = $locationRepository;
        $this->config = $config;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        $request = $this->getRequest();
        $response = new \Magento\Framework\DataObject();
        $response->setError(false);

        try{
            $locationId = $request->getParam('locationId');
            $productIds = (array)$request->getParam('productIds', []);

            if (empty($productIds['selected']) || !is_array($productIds['selected'])) {
                throw new LocalizedException(__('Please, specify product'));
            }

            //load vendor_id by location_id
            $location = $this->locationRepository->getById($locationId);
            if (empty($location) || 0==$location->getId()) {
                throw new LocalizedException(__('Invalid location'));
            }
            $vendorId = $location->getVendorId();
            if (empty($vendorId)) {
                throw new LocalizedException(__('Invalid vendor_id with location'));
            }

            if (!$this->config->isVendorShareProducts()) {
                //Remove all inventories from other vendors' locations
                $this->inventoryResource->removeByNotInVendorIds($productIds['selected'], [$vendorId]);

                //Remove all other vendor relationship on this product
                $this->vendorResource->removeProductNotInVendorIds($productIds['selected'], [$vendorId]);
            }

            //update vendor product relation
            //prepare product_id to vendor_id array
            $productIdToVendorId = [];
            foreach($productIds['selected'] as $productId) {
                $productIdToVendorId[] = [
                    'product_id' => $productId,
                    'vendor_id' => $vendorId
                ];
            }
            //save product_id to vendor_id array
            $this->vendorResource->saveProductRelation($productIdToVendorId);

            //To add location_id, product_id, qty into omnyfy_vendor_inventory table
            $this->inventoryResource->addProductIdsToLocation($productIds['selected'], $locationId);
            
        }
        catch (LocalizedException $e) {
            $response->setError(true);
            $response->setMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            $response->setError(true);
            $response->setMessage(__('Unable to add product'));
        }

        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}