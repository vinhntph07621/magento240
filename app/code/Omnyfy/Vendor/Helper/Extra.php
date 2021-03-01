<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 27/8/18
 * Time: 2:51 PM
 */
namespace Omnyfy\Vendor\Helper;

use Magento\Framework\Exception\LocalizedException;

class Extra extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_extFields = [
        'location_id' => 'session_location_id',
        'vendor_id' => 'session_vendor_id',
        'ship_from_warehouse' => 'ship_from_warehouse_flag'
    ];

    protected $_locationResource;

    protected $_resource;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Omnyfy\Vendor\Model\Resource\Location $locationResource,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        parent::__construct($context);
        $this->_locationResource = $locationResource;
        $this->_resource = $resource;
    }

    public function getExtFields()
    {
        return $this->_extFields;
    }

    protected function decodeExtraInfo($info)
    {
        $ext = json_decode($info, true);
        return empty($ext) ? [] : $ext;
    }
    public function parseExtraInfo($info, &$stockItem)
    {
        $ext = $this->decodeExtraInfo($info);

        foreach($this->getExtFields() as $k => $f) {
            if (isset($ext[$k])) {
                $stockItem->setData($f, $ext[$k]);
            }
        }
    }

    public function getSessionVendorId($quote)
    {
        $ext = $this->decodeExtraInfo($quote->getExtShippingInfo());

        return isset($ext['vendor_id']) ? intval($ext['vendor_id']) : null;
    }

    public function getShipFromWarehouseFlag($info)
    {
        $ext = $this->decodeExtraInfo($info);

        return isset($ext['ship_from_warehouse']) ? boolval($ext['ship_from_warehouse']) : false;
    }

    public function validateLocation($quote)
    {
        $shipFromWarehouse = $this->getShipFromWarehouseFlag($quote->getExtShippingInfo());
        $warehouseIds = $this->_locationResource->getWarehouseIds();

        $keys = [];
        $locationIds = [];
        $addresses = $quote->getAllShippingAddresses();
        foreach ($addresses as $address) {
            $itemCnt = 0;
            foreach ($address->getItemsCollection() as $item) {
                if ($item->isDeleted()) {
                    continue;
                }
                $keys[$address->getId() . '_' . $item->getLocationId()] = 1;
                $locationIds[$item->getLocationId()] = 1;
                $itemCnt ++;
            }
            if (0 == $itemCnt) {
                //throw new LocalizedException(__('A shipping address without item assigned.'));
                $quote->removeAddress($address->getId());
            }
        }

        $locationIds = array_keys($locationIds);
        if ($shipFromWarehouse && count(array_diff($locationIds, $warehouseIds)) > 0) {
            throw new LocalizedException(__('Store only item remains.'));
        }
        if (!$shipFromWarehouse && count(array_intersect($locationIds, $warehouseIds)) > 0) {
            throw new LocalizedException(__('Warehouse item remains.'));
        }

        $vendorIdByLocationId = $this->_locationResource->getVendorIdsByLocationIds($locationIds);
        foreach($locationIds as $locationId) {
            if (!array_key_exists($locationId, $vendorIdByLocationId)) {
                throw new LocalizedException(__('Location %1 not exist', $locationId));
            }
        }
    }

    public function validateQuoteItems($quote)
    {
        $quoteItemIdAssigned = [];
        $addresses = $quote->getAllShippingAddresses();
        foreach($addresses as $address) {
            foreach($address->getItemsCollection() as $item) {
                if ($item->isDeleted()) {
                    continue;
                }
                if (array_key_exists($item->getQuoteItemId(), $quoteItemIdAssigned)) {
                    $quoteItemIdAssigned[$item->getQuoteItemId()] += $item->getQty();
                }
                else{
                    $quoteItemIdAssigned[$item->getQuoteItemId()] = $item->getQty();
                }
            }
        }
        foreach($quote->getAllItems() as $item) {
            if ($item->isDeleted()) {
                continue;
            }
            if (!array_key_exists($item->getId(), $quoteItemIdAssigned)) {
                throw new LocalizedException(__('Item in cart not been assigned to any address'));
            }
            elseif ($quoteItemIdAssigned[$item->getId()] != $item->getQty()){
                throw new LocalizedException(__('Item %1 in not been fully assigned', $item->getSku()));
            }
        }
    }

    public function getAddressLocationIds($address)
    {
        $locationIds = [];

        foreach($address->getItemsCollection() as $item) {
            $locationIds[$item->getLocationId()] = 1;
        }

        return array_keys($locationIds);
    }

    public function updateAddressItemLocationId($quoteItemId, $locationId)
    {
        if (empty($quoteItemId)) {
            return;
        }

        $conn = $this->_resource->getConnection();
        $table = $conn->getTableName('quote_address_item');
        if ($conn->tableColumnExists($table, 'location_id')) {
            $conn->update(
                $table,
                [ 'location_id' => $locationId ],
                [ 'quote_item_id=?' => $quoteItemId ]
            );
        }
    }

    public function getJoinedErrorMsg($quote)
    {
        $errors = $quote->getErrors();
        if (empty($errors)) {
            return '';
        }

        $text = '';
        foreach($errors as $msg) {
            $text .= $msg->getText() . "\n";
        }
        return $text;
    }

    public function updateExtraInfo($quoteId, $extraInfo)
    {
        $conn = $this->_resource->getConnection();
        $table = $conn->getTableName('quote');

        $data = [
            'ext_shipping_info' => json_encode($extraInfo)
        ];

        $conn->update($table, $data, ['entity_id=?' => $quoteId]);
    }
}
