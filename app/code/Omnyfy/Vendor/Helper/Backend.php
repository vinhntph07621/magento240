<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 3/8/17
 * Time: 12:16 PM
 */
namespace Omnyfy\Vendor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Framework\App\State as AppState;
use Magento\Backend\App\Area\FrontNameResolver;

class Backend extends AbstractHelper
{
    protected $backendSession;

    protected $appState;

    protected $resource;

    protected $vendorFactory;

    protected $config;

    protected $profileResource;

    protected $_backendUrl;

    protected $_itemCollectionFactory;

    public function __construct(Context $context,
        BackendSession $backendSession,
        AppState $appState,
        \Magento\Framework\App\ResourceConnection $resource,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Omnyfy\Vendor\Model\Config $config,
        \Omnyfy\Vendor\Model\Resource\Profile $profileResource,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory
    )
    {
        $this->backendSession = $backendSession;

        $this->appState = $appState;

        $this->resource = $resource;

        $this->vendorFactory = $vendorFactory;

        $this->config = $config;

        $this->profileResource = $profileResource;

        $this->_backendUrl = $backendUrl;

        $this->_itemCollectionFactory = $itemCollectionFactory;

        parent::__construct($context);
    }

    public function getBackendVendorId()
    {
        if (FrontNameResolver::AREA_CODE != $this->appState->getAreaCode()) {
            return 0;
        }
        $vendorInfo = $this->backendSession->getVendorInfo();

        if (empty($vendorInfo) || !isset($vendorInfo['vendor_id'])) {
            return 0;
        }

        return intval($vendorInfo['vendor_id']);
    }

    protected function getVendorTotal($type, $id, $vendorId=null) {
        $conn = $this->resource->getConnection('core_write');

        $condition = 'vendor_id IS NULL';
        switch($type) {
            case 'order':
                $tableName = 'omnyfy_vendor_order_total';
                $condition = 'order_id=?';
                break;
            case 'invoice':
                $tableName = 'omnyfy_vendor_invoice_total';
                $condition = 'invoice_id=?';
                break;
        }

        $totalTable = $this->resource->getTableName($tableName);

        $select = $conn->select()->from($totalTable)
            ->where($condition, $id)
        ;
        if (!is_null($vendorId)) {
            $select->where('vendor_id=?', $vendorId);
        }

        $rows = $conn->fetchAll($select);

        if (empty($rows)) {
            return false;
        }

        $result = [];
        foreach($rows as $row) {
            $result[$row['vendor_id']] = $row;
        }
        return $result;
    }

    public function getVendorOrderTotal($orderId, $vendorId=null)
    {
        return $this->getVendorTotal('order', $orderId, $vendorId);
    }

    public function getVendorsByIds($vendorIds)
    {
        if (empty($vendorIds)) {
            return false;
        }
        if (!is_array($vendorIds)) {
            $vendorIds = [intval($vendorIds)];
        }
        $collection = $this->vendorFactory->create()->getCollection();
        $collection->addFieldToFilter('entity_id', $vendorIds);

        return $collection;
    }

    public function getVendorInvoiceTotal($invoiceId, $vendorId=null)
    {
        return $this->getVendorTotal('invoice', $invoiceId, $vendorId);
    }

    public function getVendorTotalData($object)
    {
        $result = [];
        $vendorTotal = null;
        $type = '';
        if ($object instanceof \Magento\Sales\Model\Order) {
            $type = 'Magento\Sales\Model\Order';
            $vendorTotal = $this->getVendorOrderTotal($object->getId());
        }
        elseif ($object instanceof \Magento\Sales\Model\Order\Invoice) {
            $type = 'Magento\Sales\Model\Order\Invoice';
            $vendorTotal = $this->getVendorInvoiceTotal($object->getId());
        }

        if (empty($vendorTotal)) {
            return $result;
        }

        $vendors = $this->getVendorsByIds(array_keys($vendorTotal));
        $vendorId = $this->getBackendVendorId();

        foreach($vendorTotal as $vId => $total) {
            if (!empty($vendorId) && $vId !== $vendorId) continue;
            $vendor = $vendors->getItemById($vId);
            if (empty($vendor)) continue;

            $_value = $total['subtotal'] + $total['shipping_amount'];
            if (empty($vendorId)) {
                // show total for each vendor
                $_label = 'Total for ' . $vendor->getName();

                $result[] = new \Magento\Framework\DataObject(
                    [
                        'label' => $_label,
                        'value' => $_value,
                        'base_value' => $_value,
                        $type => $object
                    ]
                );
            }
            else{
                // for one vendor, show subtotal, shipping and vendor total
                $result[] = new \Magento\Framework\DataObject(
                    [
                        'code' => 'my_subtotal',
                        'label' => 'Subtotal',
                        'value' => $total['subtotal'],
                        'base_value' => $total['subtotal'],
                        $type => $object
                    ]
                );
                $result[] = new \Magento\Framework\DataObject(
                    [
                        'code' => 'my_shipping',
                        'label' => 'Shipping',
                        'value' => $total['shipping_amount'],
                        'base_value' => $total['shipping_amount'],
                        $type => $object
                    ]
                );
                $result[] = new \Magento\Framework\DataObject(
                    [
                        'code' => 'my_tax',
                        'label' => 'Tax',
                        'value' => $total['tax_amount'],
                        'base_value' => $total['tax_amount'],
                        $type => $object
                    ]
                );
                $result[] = new \Magento\Framework\DataObject(
                    [
                        'code' => 'my_grand_total',
                        'label' => 'Grand Total',
                        'value' => $_value,
                        'base_value' => $_value,
                        $type => $object
                    ]
                );
            }
        }
        return $result;
    }

    public function isVendorShareProducts()
    {
        return $this->config->isVendorShareProducts();
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     */
    public function isOrderAllCredited($order){
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $orderItems */
        $orderItems = $this->getOrderItems($order);

        foreach($orderItems as $item){
            if ($item->canRefund())
                return false;
        }

        return true;
    }


    /**
     * @param $order
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getOrderItems($order){
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $collection */
        $collection = $this->_itemCollectionFactory->create();
        $collection->setFlag("all_items", true);
        $collection->addFieldToFilter('order_id',['eq' => $order->getId()]);
        $collection->load();
        $collection->setFlag("all_items", false);

        return $collection;
    }


    public function isOrderAllShipped($order)
    {
        $toShip = $this->getToShipQtyByOrderId($order->getId());
        $items = $order->getAllItems();
        foreach($items as $item) {
            if ($item->getQtyToShip() > 0 && !$item->getIsVirtual() && !$item->getLockedDoShip()) {
                return false;
            }
            if (array_key_exists($item->getId(), $toShip)) {
                unset($toShip[$item->getId()]);
            }
        }

        if (!empty($toShip)) {
            return false;
        }

        return true;
    }

    public function getToShipQtyByOrderId($orderId)
    {
        $conn = $this->resource->getConnection('core_read');

        $itemTable = $conn->getTableName('sales_order_item');
        $select = $conn->select()->from($itemTable)
            ->where('order_id=?', $orderId)
        ;

        $rows = $conn->fetchAll($select);

        $result = [];
        foreach($rows as $row) {
            $itemId = $row['item_id'];
            $qty = $row['qty_ordered'] - $row['qty_shipped'] - $row['qty_refunded'] - $row['qty_canceled'];
            $qty = max($qty, 0);
            if ($qty > 0 && !boolval($row['is_virtual']) && !boolval($row['locked_do_ship'])) {
                $result[$itemId] = $qty;
            }
        }
        return $result;
    }

    public function getAttributeSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }

    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    public function getAttributeInputTypes($inputType = null)
    {
        /**
         * @todo specify there all relations for properties depending on input type
         */
        $inputTypes = [
            'multiselect' => [
                'backend_model' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Table'
            ],
            'boolean' => ['source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean'],
            'image' => ['backend_model' => 'Omnyfy\Vendor\Model\Vendor\Attribute\Backend\Media'],
        ];

        if ($inputType === null) {
            return $inputTypes;
        } else {
            if (isset($inputTypes[$inputType])) {
                return $inputTypes[$inputType];
            }
        }
        return [];
    }

    public function updateWebsiteIds($vendorId, $websiteIds, $data)
    {
        $toRemove = [];

        $toRemove['vendor_id'] = $vendorId;
        $toRemove['website_id'] = array_diff($websiteIds, $data);
        $this->profileResource->remove($toRemove);

        $toAddIds = array_diff($data, $websiteIds);
        $toAdd = [];
        $zendNull = new \Zend_Db_Expr('NULL');
        foreach($toAddIds as $websiteId) {
            $toAdd[] = [
                'profile_id' => $zendNull,
                'vendor_id' => $vendorId,
                'website_id' => $websiteId,
                'updates' => ''
            ];
        }
        $this->profileResource->bulkSave($toAdd);
    }

    public function getWebsiteIdsByVendorId($vendorId)
    {
        $websiteIds = $this->profileResource->getProfileIdsByVendorId($vendorId);
        return empty($websiteIds) ? [] : array_keys($websiteIds);
    }

    public function getRequest()
    {
        return $this->_getRequest();
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->_backendUrl->getUrl($route, $params);
    }

    public function isVendor()
    {
        $vendorInfo = $this->backendSession->getVendorInfo();
        if (!empty($vendorInfo)) {
            return true;
        }
        return false;
    }
}