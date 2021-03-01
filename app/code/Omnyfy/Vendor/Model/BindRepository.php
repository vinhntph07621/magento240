<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 19/11/18
 * Time: 2:11 AM
 */
namespace Omnyfy\Vendor\Model;

class BindRepository implements \Omnyfy\Vendor\Api\BindRepositoryInterface
{
    protected $vendorResource;

    protected $vendorHelper;

    protected $quoteRepository;

    protected $_allStores;

    protected $_resource;

    protected $moduleManager;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Omnyfy\Vendor\Helper\Data $vendorHelper,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Module\Manager $moduleManager
    )
    {
        $this->vendorResource = $vendorResource;
        $this->vendorHelper = $vendorHelper;
        $this->quoteRepository = $quoteRepository;
        $this->_resource = $resource;
        $this->moduleManager = $moduleManager;
    }

    public function save($customerId, $vendorId)
    {
        if (empty($customerId)) {
            return ['error' => true, 'message' => 'For logged in customer only.'];
        }

        $allStores = $this->getAllStores();
        if (empty($vendorId)) {
            return ['error' => true, 'message' => 'Invalid request'];
        }

        try {
            $this->vendorResource->saveFavoriteVendorId($customerId, $vendorId);

            $quote = $this->quoteRepository->getActiveForCustomer($customerId);
            $extInfo = $quote->getExtShippingInfo();
            $extInfo = empty($extInfo) ? [] : json_decode($extInfo, true);

            if ($this->moduleManager->isEnabled('Omnyfy_Multishipping')) {
                if (!isset($extInfo['ship_from_warehouse'])) {
                    $extInfo['ship_from_warehouse'] = true;
                }
            }
            
            $extInfo['vendor_id'] = $vendorId;
            $this->updateExtraInfo($quote->getId(), $extInfo);

            return ['success' => true, 'message' => 'Saved your selection'];
        }
        catch (\Magento\Framework\Exception\NoSuchEntityException $exception)
        {
            return ['success' => true, 'message' => 'Saved your selection'];
        }
        catch (\Exception $e)
        {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    protected function getAllStores()
    {
        if (is_null($this->_allStores)) {
            $this->_allStores = $this->vendorHelper->getAllStores();
        }
        return $this->_allStores;
    }

    protected function updateExtraInfo($quoteId, $extraInfo)
    {
        $conn = $this->_resource->getConnection();
        $table = $conn->getTableName('quote');

        $data = [
            'ext_shipping_info' => json_encode($extraInfo)
        ];

        $conn->update($table, $data, ['entity_id=?' => $quoteId]);
    }
}
