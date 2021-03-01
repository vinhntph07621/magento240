<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Adminhtml reviews grid
 *
 * @method int getVendorId() getVendorId()
 * @method \Omnyfy\VendorReview\Block\Adminhtml\Grid setVendorId() setVendorId(int $vendorId)
 * @method int getCustomerId() getCustomerId()
 * @method \Omnyfy\VendorReview\Block\Adminhtml\Grid setCustomerId() setCustomerId(int $customerId)
 * @method \Omnyfy\VendorReview\Block\Adminhtml\Grid setMassactionIdFieldOnlyIndexValue() setMassactionIdFieldOnlyIndexValue(bool $onlyIndex)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Omnyfy\VendorReview\Block\Adminhtml;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Review action pager
     *
     * @var \Omnyfy\VendorReview\Helper\Action\Pager
     */
    protected $_reviewActionPager = null;

    /**
     * Review data
     *
     * @var \Omnyfy\VendorReview\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Review collection model factory
     *
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\CollectionFactory
     */
    protected $_vendorsFactory;

    /**
     * Review model factory
     *
     * @var \Omnyfy\VendorReview\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\CollectionFactory $vendorsFactory
     * @param \Omnyfy\VendorReview\Helper\Data $reviewData
     * @param \Omnyfy\VendorReview\Helper\Action\Pager $reviewActionPager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory,
        \Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\CollectionFactory $vendorsFactory,
        \Omnyfy\VendorReview\Helper\Data $reviewData,
        \Omnyfy\VendorReview\Helper\Action\Pager $reviewActionPager,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_vendorsFactory = $vendorsFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_reviewData = $reviewData;
        $this->_reviewActionPager = $reviewActionPager;
        $this->_reviewFactory = $reviewFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ratingsGrid');
        $this->setDefaultSort('created_at');
    }

    /**
     * Save search results
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _afterLoadCollection()
    {
        /** @var $actionPager \Omnyfy\VendorReview\Helper\Action\Pager */
        $actionPager = $this->_reviewActionPager;
        $actionPager->setStorageId('reviews');
        $actionPager->setItems($this->getCollection()->getResultingIds());

        return parent::_afterLoadCollection();
    }

    /**
     * Prepare collection
     *
     * @return \Omnyfy\VendorReview\Block\Adminhtml\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $model \Omnyfy\VendorReview\Model\Review */
        $model = $this->_reviewFactory->create();
        /** @var $collection \Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\Collection */
        $collection = $this->_vendorsFactory->create();

        if ($this->getVendorId() || $this->getRequest()->getParam('vendorId', false)) {
            $vendorId = $this->getVendorId();
            if (!$vendorId) {
                $vendorId = $this->getRequest()->getParam('vendorId');
            }
            $this->setVendorId($vendorId);
            $collection->addEntityFilter($this->getVendorId());
        }

        if ($this->getCustomerId() || $this->getRequest()->getParam('customerId', false)) {
            $customerId = $this->getCustomerId();
            if (!$customerId) {
                $customerId = $this->getRequest()->getParam('customerId');
            }
            $this->setCustomerId($customerId);
            $collection->addCustomerFilter($this->getCustomerId());
        }

        if ($this->_coreRegistry->registry('usePendingFilter') === true) {
            $collection->addStatusFilter($model->getPendingStatus());
        }

        $collection->addStoreData();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Backend\Block\Widget\Grid
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'omnyfy_vendor_review_id',
            [
                'header' => __('ID'),
                'filter_index' => 'rt.omnyfy_vendor_review_id',
                'index' => 'omnyfy_vendor_review_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Business Name'),
                'type' => 'text',
                'index' => 'name',
                'escape' => true
            ]
        );

        $this->addColumn(
            'nickname',
            [
                'header' => __('Nickname'),
                'filter_index' => 'rdt.nickname',
                'index' => 'nickname',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true,
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Review Title'),
                'filter_index' => 'rdt.title',
                'index' => 'title',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true
            ]
        );

        $this->addColumn(
            'detail',
            [
                'header' => __('Review Detail'),
                'index' => 'detail',
                'filter_index' => 'rdt.detail',
                'type' => 'text',
                'truncate' => 50,
                'nl2br' => true,
                'escape' => true
            ]
        );

        if (!$this->_coreRegistry->registry('usePendingFilter')) {
            $this->addColumn(
                'status',
                [
                    'header' => __('Status'),
                    'type' => 'options',
                    'options' => $this->_reviewData->getReviewStatuses(),
                    'filter_index' => 'rt.status_id',
                    'index' => 'status_id'
                ]
            );
        }

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getOmnyfyVendorReviewId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'vendorreview/vendor/edit',
                            'params' => [
                                'vendorId' => $this->getVendorId(),
                                'customerId' => $this->getCustomerId(),
                                'ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : null,
                            ],
                        ],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid mass actions
     *
     * @return void
     */
    protected function _prepareMassaction()
    {
        //$this->setMassactionIdField('omnyfy_vendor_review_id');
        $this->setMassactionIdFilter('rt.omnyfy_vendor_review_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('reviews');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl(
                    '*/*/massDelete',
                    ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']
                ),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_reviewData->getReviewStatusesOptionArray();
        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(
            'update_status',
            [
                'label' => __('Update Status'),
                'url' => $this->getUrl(
                    '*/*/massUpdateStatus',
                    ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']
                ),
                'additional' => [
                    'status' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses,
                    ],
                ]
            ]
        );
    }

    /**
     * Get row url
     *
     * @param \Omnyfy\VendorReview\Model\Review|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'vendorreview/vendor/edit',
            [
                'id' => $row->getOmnyfyVendorReviewId(),
                'vendorId' => $this->getVendorId(),
                'customerId' => $this->getCustomerId(),
                'ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : null
            ]
        );
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        if ($this->getVendorId() || $this->getCustomerId()) {
            return $this->getUrl(
                'vendorreview/vendor' . ($this->_coreRegistry->registry('usePendingFilter') ? 'pending' : ''),
                ['vendorId' => $this->getVendorId(), 'customerId' => $this->getCustomerId()]
            );
        } else {
            return $this->getCurrentUrl();
        }
    }
}
