<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-24
 * Time: 16:55
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Location\Attribute;

use Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid;

class Grid extends AbstractGrid
{
    /**
     * @var \Omnyfy\Vendor\Model\Resource\Location\Attribute\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Omnyfy\Vendor\Model\Resource\Location\Attribute\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Omnyfy\Vendor\Model\Resource\Location\Attribute\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_module = 'omnyfy_vendor';
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Prepare location attributes grid collection object
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create()->addVisibleFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare product attributes grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter(
            'is_visible',
            [
                'header' => __('Visible'),
                'sortable' => true,
                'index' => 'is_visible_on_front',
                'type' => 'options',
                'options' => ['1' => __('Yes'), '0' => __('No')],
                'align' => 'center'
            ],
            'frontend_label'
        );

        $this->addColumnAfter(
            'is_global',
            [
                'header' => __('Scope'),
                'sortable' => true,
                'index' => 'is_global',
                'type' => 'options',
                'options' => [
                    \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE => __('Store View'),
                    \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE => __('Web Site'),
                    \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL => __('Global'),
                ],
                'align' => 'center'
            ],
            'is_visible'
        );

        $this->addColumn(
            'is_searchable',
            [
                'header' => __('Searchable'),
                'sortable' => true,
                'index' => 'is_searchable',
                'type' => 'options',
                'options' => ['1' => __('Yes'), '0' => __('No')],
                'align' => 'center'
            ]
        );

        $this->_eventManager->dispatch('location_attribute_grid_build', ['grid' => $this]);

        return $this;
    }
}
 