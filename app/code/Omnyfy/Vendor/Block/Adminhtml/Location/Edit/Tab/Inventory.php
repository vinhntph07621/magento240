<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/8/17
 * Time: 4:03 PM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Location\Edit\Tab;

class Inventory extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $productCollectionFactory;

    protected $vendorResource;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = [])
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->vendorResource = $vendorResource;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct() {
        parent::_construct();
        $this->setId('inventoryGrid');
        $this->setDefaultSort('entity_id');
        //$this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_product' => 1]);
        }
    }

    protected function _prepareCollection() {
        $locationId = $this->getRequest()->getParam('id');
        $locationId = intval($locationId);

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');

        $from = $collection->getSelect()->getPart(\Zend_Db_Select::FROM);
        $alias = (isset($from['e'])) ? 'e' : 'main_table';

        $collection->getSelect()
            ->join(
                ['i' => 'omnyfy_vendor_inventory'],
                "{$alias}.entity_id=i.product_id AND i.location_id={$locationId}",
                ['qty' => 'i.qty', 'product_id' => 'i.product_id']
            );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        /*
        $this->addColumn(
            'in_products',
            [
                'type' => 'checkbox',
                'html_name' => 'products_id',
                'required' => true,
                'values' => $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'entity_id',
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select'
            ]
        );
        */

        $this->addColumn(
            'entity_id', [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'name', [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'sku', [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'qty', [
                'header' => __('Available Inventory'),
                'type' => 'number',
                'index' => 'qty',
                'editable' => true,
                'edit_only' => true,
                'width' => '50px',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/inventory', ['_current' => true]);
    }

    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row) {
        return '';
    }

    protected function _getSelectedProducts() {
        return array_keys($this->getSelectedProducts());
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedProducts() {
        $locationId = $this->getRequest()->getParam('id');

        return $this->vendorResource->getInventoryByLocationId($locationId);
    }



    /**
     * {@inheritdoc}
     */
    public function canShowTab() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden() {
        return true;
    }
}