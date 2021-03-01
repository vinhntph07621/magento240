<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 10:25
 */
namespace Omnyfy\VendorSubscription\Block\Adminhtml\Vendor\Edit\Tab;

class Subscription extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $collectionFactory;

    protected $subscription;

    protected $planResource;

    protected $planCollectionFactory;

    protected $subStatus;

    protected $interval;

    protected $planFactory;

    protected $fieldMap = [
        'subscription_id' => 'id',
        'sub_status' => 'status',
        'plan_id' => 'plan_id',
        'plan_price' => 'plan_price',
        'billing_interval' => 'billing_interval',
        'show_on_front' => 'show_on_front',
        'next_billing_at' => 'next_billing_at',
        'cancelled_at' => 'cancelled_at',
        'expiry_at' => 'expiry_at',
        'sub_description' => 'description',
    ];

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Omnyfy\VendorSubscription\Model\Resource\Subscription\CollectionFactory $collectionFactory,
        \Omnyfy\VendorSubscription\Model\Resource\Plan $planResource,
        \Omnyfy\VendorSubscription\Model\Resource\Plan\CollectionFactory $planCollectionFactory,
        \Omnyfy\VendorSubscription\Model\Source\SubscriptionStatus $subscriptionStatus,
        \Omnyfy\VendorSubscription\Model\Source\Interval $interval,
        \Omnyfy\VendorSubscription\Model\PlanFactory $planFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->planResource = $planResource;
        $this->planCollectionFactory = $planCollectionFactory;
        $this->subStatus = $subscriptionStatus;
        $this->interval = $interval;
        $this->planFactory = $planFactory;
        parent::__construct($context, $registry, $formFactory, $data);
        $vendor = $this->_coreRegistry->registry('current_omnyfy_vendor_vendor');

        if (!empty($vendor)) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('vendor_id', $vendor->getId());
            $collection->setPageSize(1);
            if ($collection->getSize() > 0) {
                $this->subscription = $collection->getFirstItem();
                $this->_coreRegistry->register('current_omnyfy_subscription_subscription', $this->subscription);
            }
        }
    }

    public function getTabLabel()
    {
        return __('Vendor Subscription');
    }

    public function getTabTitle()
    {
        return __('Vendor Subscription');
    }

    public function canShowTab()
    {
        if (empty($this->subscription)) {
            return false;
        }
        return true;
    }

    public function isHidden()
    {
        if (empty($this->subscription)) {
            return true;
        }
        return false;
    }

    protected function _prepareForm()
    {
        if (empty($this->subscription)) {
            return parent::_prepareForm();
        }

        $vendor = $this->_coreRegistry->registry('current_omnyfy_vendor_vendor');

        foreach($this->fieldMap as $vendorField => $subField) {
            $vendor->setData($vendorField, $this->subscription->getData($subField));
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('vendor_');

        $fieldset = $form->addFieldset('subscription_fieldset', ['legend' => __('Vendor Subscription')]);

        $fieldset->addField('subscription_id', 'hidden', ['name'=> 'subscription_id']);
        $fieldset->addField('plan_id', 'hidden', ['name'=> 'plan_id']);


        $fieldset->addField(
            'sub_status',
            'select',
            [
                'name' => 'sub_status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'disabled' => true,
                'options' => $this->subStatus->toValuesArray()
            ]
        );

        $plan = $this->planFactory->create()->load($vendor->getPlanId());
        if (!$plan->getIsFree()) {
            $fieldset->addField(
                'next_billing_at',
                'date',
                [
                    'name' => 'next_billing_at',
                    'label' => __('Next Billing'),
                    'title' => __('Next Billing'),
                    'required' => true,
                    'date_format' => 'yyyy-MM-dd',
                    'time_format' => '00:00:00',
                    'disabled' => true,
                ]
            );
        }


        $fieldset->addField(
            'cancelled_at',
            'date',
            [
                'name' => 'cancelled_at',
                'label' => __('Cancelled on'),
                'title' => __('Cancelled on'),
                'required' => true,
                'date_format' => 'yyyy-MM-dd',
                'time_format' => 'HH:mm:ss',
                'disabled' => true,
            ]
        );

        $fieldset->addField(
            'expiry_at',
            'date',
            [
                'name' => 'expiry_at',
                'label' => __('Expiry Date'),
                'title' => __('Expiry Date'),
                'required' => true,
                'date_format' => 'yyyy-MM-dd',
                'time_format' => '00:00:00',
                'disabled' => true,
            ]
        );

        $layoutBlock = $this->getLayout()->createBlock(
            'Omnyfy\VendorSubscription\Block\Adminhtml\Vendor\Renderer\Plan'
        )
            ->setSubscription($this->subscription)
            ->setVendor($vendor)
        ;

        $fieldset->addField('plan', 'note', []);
        $form->getElement('plan')->setRenderer($layoutBlock);

        $fieldset->addField(
            'plan_price',
            'text',
            [
                'name' => 'plan_price',
                'label' => __('Plan Price'),
                'title' => __('Plan Price'),
                'required' => true,
                'disabled' => true,
            ]
        );

        $fieldset->addField(
            'billing_interval',
            'select',
            [
                'name' => 'billing_interval',
                'label' => __('Billing Interval'),
                'title' => __('Billing Interval'),
                'required' => true,
                'disabled' => true,
                'options' => $this->interval->toValuesArray()
            ]
        );

        $fieldset->addField(
            'sub_description',
            'text',
            [
                'name' => 'sub_description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => true,
                'disabled' => true,
            ]
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
                ->addFieldMap(
                    'vendor_sub_status',
                    'sub_status'
                )
                ->addFieldMap(
                    'vendor_next_billing_at',
                    'next_billing_at'
                )
                ->addFieldMap(
                    'vendor_cancelled_at',
                    'cancelled_at'
                )
                ->addFieldMap(
                    'vendor_expiry_at',
                    'expiry_at'
                )
                ->addFieldDependence(
                    'next_billing_at',
                    'sub_status',
                    '1'
                )
                ->addFieldDependence(
                    'cancelled_at',
                    'sub_status',
                    '2'
                )
                ->addFieldDependence(
                    'expiry_at',
                    'sub_status',
                    '2'
                )
        );

        //$gridFieldset = $form->addFieldset('history_fieldset', ['legend' => __('Subscription History')]);

        $form->setValues($vendor->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function getPlanOptionsByVendorTypeId($vendorTypeId)
    {
        $rolePlans = $this->planResource->getRolePlanByVendorTypeId($vendorTypeId);
        if (empty($rolePlans)) {
            return [];
        }

        $planIds = [];
        foreach($rolePlans as $row) {
            $planIds[] = $row['plan_id'];
        }
        $collection = $this->planCollectionFactory->create();
        $collection->addFieldToFilter('plan_id', ['in' => $planIds]);
        $result = [];
        foreach($collection as $plan) {
            $result[$plan->getId()] = $plan->getPlanName();
        }
        return $result;
    }
/*
    protected function _toHtml()
    {
        $form = $this->getForm();

        $grid = $this->getLayout()->createBlock('Omnyfy\VendorSubscription\Block\Adminhtml\Vendor\Edit\Tab\History', 'history.grid');

        $html = "
<div class=\"entry-edit\">
{$form->toHtml()}
</div>
<div class=\"entry-edit\">
<div class=\"entry-edit-head\">
    <h4 class=\"icon-head head-edit-form fieldset-legend\">History</h4>
</div>
<div class=\"fieldset \">
    <div class=\"hor-scroll\">
        {$grid->toHtml()}
    </div>
</div>
</div>
        ";

        return $html;
    }
*/
}
 