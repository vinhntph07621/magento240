<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 11/7/17
 * Time: 11:18 AM
 */

namespace Omnyfy\Vendor\Block\Adminhtml\Shipment\Location;

use Magento\Backend\Model\Session as BackendSession;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $collectionFactory;

    protected $orderRepository;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $collectionFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct() {
        parent::_construct();
        $this->setId('shipment_location_grid');
        $this->setDefaultSort('location_id');
        //$this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        if ($this->getRequest()->getParam('order_id')) {
            //$this->setDefaultFilter(['in_product' => 1]);
        }

        $grouped = $this->getGroupedItems();
        $this->setAdditionalJavaScript('function to_ship(location_id){
        var items = ' . json_encode($grouped) . ';

        if (items[location_id]) {
        console.log(items[location_id]);
            var form = document.createElement("form");
            form.setAttribute("method", "POST");
            form.setAttribute("action", this.href);
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", "shipment[items]");
            hiddenField.setAttribute("value", JSON.stringify(items[location_id]));
            form.appendChild(hiddenField);
            document.body.appendChild(form);
            form.submit();
        }
        return false;
        }');
    }

    protected function _prepareCollection()
    {
        $items = $this->getGroupedItems();
        $locationIds = array_keys($items);

        //filter collection by user if it's a vendor
        $vendorInfo = $this->_backendSession->getVendorInfo();
        if (isset($vendorInfo['location_ids'])) {
            $locationIds = array_intersect($locationIds, $vendorInfo['location_ids']);
        }

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['in' => $locationIds]);
        $collection->joinVendorInfo();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'index' => 'entity_id',
                'class' => '',
                'width' => '50px'
            ]
        );

        $this->addColumn(
            'name',[
                'header' => __('Location'),
                'index' => 'location_name',
                'class' => '',
                'width' => '50px'
            ]
        );

        $this->addColumn('vendor_name',
            [
                'header' => __('Vendor Name'),
                'index' => 'vendor_name',
                'class' => '',
                'width' => '50px'
            ]
        );

        $this->addColumn('order_items',
            [
                'header' => __('Order Items'),
                'sortable' => false,
                'index' => 'location_id',
                'renderer' => 'Omnyfy\Vendor\Block\Adminhtml\Shipment\Location\OrderItems'
            ]
        );

        $this->addColumn('actions',
            [
                'header' => __('Ship'),
                'sortable' => false,
                'index' => 'location_id',
                'renderer' => 'Omnyfy\Vendor\Block\Adminhtml\Shipment\Location\ToShip'
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGroupedItems() {
        $orderId = $this->getRequest()->getParam('order_id');
        $order = $this->orderRepository->get($orderId);
        $grouped = [];
        foreach($order->getItems() as $item) {
            if ($item->getQtyToShip() <= 0 ) {
                continue;
            }
            $locationId = $item->getLocationId();
            if (!isset($grouped[$locationId])) {
                $grouped[$locationId] = [];
            }
            $grouped[$locationId][$item->getId()] = $item->getQtyToShip();
        }

        return $grouped;
    }

}