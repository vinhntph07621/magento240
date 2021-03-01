<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-13
 * Time: 10:23
 */
namespace Omnyfy\VendorSubscription\Block\Form;

use Magento\Framework\View\Element\Template;

class Type extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'Omnyfy_VendorSubscription::type_plan_form.phtml';

    protected $vendorTypeCollectionFactory;

    protected $planCollectionFactory;

    protected $planResource;

    protected $_vendorTypes;

    protected $_plans;

    protected $_planIdToTypeId;

    protected $helper;

    protected $intervalSource;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\Vendor\Model\Resource\VendorType\CollectionFactory $vendorTypeCollectionFactory,
        \Omnyfy\VendorSubscription\Model\Resource\Plan\CollectionFactory $planCollectionFactory,
        \Omnyfy\VendorSubscription\Model\Resource\Plan $planResource,
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        \Omnyfy\VendorSubscription\Model\Source\Interval $intervalSource,
        array $data = []
    ) {
        $this->vendorTypeCollectionFactory = $vendorTypeCollectionFactory;
        $this->planCollectionFactory = $planCollectionFactory;
        $this->planResource = $planResource;
        $this->helper = $helper;
        $this->intervalSource = $intervalSource;
        parent::__construct($context, $data);
    }

    public function getCacheKeyInfo()
    {
        $keyInfo =  parent::getCacheKeyInfo();
        $keyInfo[] = $this->getData('vendor_type_id');
        return $keyInfo;
    }

    public function loadVendorTypeCollection()
    {
        if (is_null($this->_vendorTypes)) {
            $collection = $this->vendorTypeCollectionFactory->create();
            $collection->addFieldToFilter('status', 1);
            $this->_vendorTypes = $collection;
        }
        return $this->_vendorTypes;
    }

    public function loadPlanCollection($vendorTypeId)
    {
        if (!isset($this->_plans[$vendorTypeId])) {
            $collection = $this->planCollectionFactory->create();
            $collection->addFieldToFilter('show_on_front', 1);
            $collection->addRoleIdJoin($vendorTypeId);
            $collection->addOrder('status');
            $collection->addOrder('price', 'ASC');
            $this->_plans[$vendorTypeId] = $collection;
        }
        return $this->_plans[$vendorTypeId];
    }

    public function loadPlanIdToTypeId()
    {
        if (is_null($this->_planIdToTypeId)) {
            $rows = $this->planResource->loadAllTypePlanRelation();
            $result = [];
            foreach($rows as $row) {
                $result[$row['plan_id']] = $row['type_id'];
            }
            $this->_planIdToTypeId = $result;
        }
        return $this->_planIdToTypeId;
    }

    public function formatPrice($amount, $includeContainer = true, $precision = 2)
    {
        return $this->helper->formatPrice($amount, $includeContainer, $precision);
    }

    public function getIntervalTitle($interval)
    {
        $values = $this->intervalSource->toValuesArray();
        if (array_key_exists($interval, $values)) {
            return $values[$interval];
        }

        return false;
    }

    public function parseBenefits($benefits)
    {
        $result = explode('|', $benefits);
        return empty($result) ? [] : $result;
    }

    public function getLinkUrl($vendorTypeId, $planId, $status)
    {
        if (0 == $status) {
            return '#';
        }

        return $this->getUrl('omnyfy_vendorsignup/index/index',
            [
                'type_id' => $vendorTypeId,
                'plan_id' => $planId
            ]
        );
    }
}
 