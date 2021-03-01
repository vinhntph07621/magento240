<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-11
 * Time: 11:32
 */
namespace Omnyfy\VendorSubscription\Block\Adminhtml\Vendor\Edit\Tab;

class History extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $registry;

    protected $collectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Omnyfy\VendorSubscription\Model\Resource\History\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('historyGrid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setFilterVisibility(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create();
        $vendorId = $this->getRequest()->getParam('id');
        if (!empty($vendorId)) {
            $collection->addVendorFilter($vendorId);
        }
        else {
            $vendor = $this->registry->registry('current_omnyfy_vendor_vendor');
            if (!empty($vendor)) {
                $collection->addVendorFilter($vendor->getId());
            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'plan_name',
            [
                'header' => __('Plan'),
                'index' => 'plan_name',
            ]
        );
        $this->addColumn(
            'vendor_name',
            [
                'header' => __('Vendor'),
                'index' => 'vendor_name',
            ]
        );
        $this->addColumn(
            'plan_price',
            [
                'header' => __('Price'),
                'index' => 'plan_price',
            ]
        );
        $this->addColumn(
            'billing_amount',
            [
                'header' => __('Amount'),
                'index' => 'billing_amount',
            ]
        );
        $this->addColumn(
            'billing_account_name',
            [
                'header' => __('Account Name'),
                'index' => 'billing_account_name',
            ]
        );
        $this->addColumn(
            'billing_date',
            [
                'header' => __('Billing Date'),
                'index' => 'billing_date',
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
            ]
        );
        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'index' => 'invoice_link',
                'renderer' => 'Omnyfy\VendorSubscription\Block\Adminhtml\History\Renderer\Invoice',
                'filter' => false,
                'sortable' => false,
            ]
        );
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $params = ['_current' => true];
        $vendorId = $this->getParam('id');
        if (!empty($vendorId)) {
            $params['id'] = $vendorId;
        }
        return $this->getUrl('omnyfy_subscription/history/grid', $params);
    }

    public function getRowUrl($row) {
        return '';
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }
}
 