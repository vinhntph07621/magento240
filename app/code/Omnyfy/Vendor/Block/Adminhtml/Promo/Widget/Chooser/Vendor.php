<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 30/8/18
 * Time: 2:09 PM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Promo\Widget\Chooser;

class Vendor extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_collectionFactory;

    protected $_vendorCollectionInstance;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $collectionFactory,
        array $data = []
    )
    {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        if ($this->getRequest()->getParam('current_grid_id')) {
            $this->setId($this->getRequest()->getParam('current_grid_id'));
        }
        else {
            $this->setId('vendorChooserGrid_'. $this->getId());
        }

        $form = $this->getJsFormObject();
        $this->setRowClickCallback("{$form}.chooserGridRowClick.bind({$form})");
        $this->setCheckboxCheckCallback("{$form}.chooserGridCheckboxCheck.bind({$form})");
        $this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
        $this->setDefaultSort('vendor_id');
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('collapse')) {
            $this->setIsCollapsed(true);
        }
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_vendors') {
            $selected = $this->_getSelectedVendors();
            if (empty($selected)) {
                $selected = '';
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = $this->_getVendorCollectionInstance();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _getVendorCollectionInstance()
    {
        if (!$this->_vendorCollectionInstance) {
            $this->_vendorCollectionInstance = $this->_collectionFactory->create();
        }
        return $this->_vendorCollectionInstance;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_vendors',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_vendors',
                'values' => $this->_getSelectedVendors(),
                'align' => 'center',
                'index' => 'entity_id',
                'use_index' => true
            ]
        );

        $this->addColumn(
            'entity_id',
            ['header' => __('ID'), 'sortable' => true, 'width' => '60px', 'index' => 'entity_id']
        );

        $this->addColumn(
            'chooser_name',
            ['header' => __('Vendor'), 'name' => 'chooser_name', 'index' => 'name']
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl(
            'omnyfy_vendor/*/chooser',
            ['_current' => true, 'current_grid_id' => $this->getId(), 'collapse' => null]
        );
    }

    protected function _getSelectedVendors()
    {
        $vendors = $this->getRequest()->getPost('selected', []);

        return $vendors;
    }
}