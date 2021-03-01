<?php

namespace Omnyfy\Vendor\Block\Adminhtml\Vendor\Edit\Tab;

use Omnyfy\Vendor\Model\VendorFactory;

class Products extends \Magento\Backend\Block\Widget\Grid\Extended {

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Contact factory
     *
     * @var VendorFactory
     */
    protected $vendorFactory;

    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;


    protected $vendorResource;
    /**
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Omnyfy\Vendor\Model\VendorFactory $vendorFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,

        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->vendorFactory = $vendorFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->vendorResource = $vendorResource;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct() {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        //$this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        if ($this->getRequest()->getParam('id')) {
            //$this->setDefaultFilter(['in_product' => 1]);
        }
    }


    protected function _prepareMassaction() {
        if (!empty($this->_getSelectedProducts())) {
            $this->getMassactionBlock()->setTemplate('Omnyfy_Vendor::widget/grid/massaction_extended.phtml');
            $this->setMassactionIdField('entity_id');
            $this->getMassactionBlock()->setFormFieldName('sel_product');

            $this->getMassactionBlock()->addItem(
                    'assign_location', [
                'label' => __('Assign to Location'),
                    ]
            );

            return $this;
        }
    }

    /**
     * prepare collection
     */
    protected function _prepareCollection() {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $collection->addFieldToFilter('entity_id', array(
            'in' => $this->_getSelectedProducts())
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns() {

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
                'price', [
            'header' => __('Price'),
            'type' => 'currency',
            'index' => 'price',
            'width' => '50px',
                ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl() {
        return $this->getUrl('*/*/products', ['_current' => true]);
    }

    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row) {
        return '';
    }

    protected function _getSelectedProducts() {
        return $this->getSelectedProducts();
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedProducts() {
        $vendorId = $this->getRequest()->getParam('id');

        return $this->vendorResource->getProductIdsByVendorId($vendorId);
    }

    protected function getVendor() {
        $vendorId = $this->getRequest()->getParam('id');
        $vendor = $this->vendorFactory->create();
        if ($vendorId) {
            $vendor->load($vendorId);
        }
        return $vendor;
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
