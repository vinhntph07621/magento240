<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-01
 * Time: 15:41
 */
namespace Omnyfy\VendorSubscription\Block\Form;

use Magento\Framework\View\Element\Template;

class Basic extends \Magento\Framework\View\Element\Template
{
    protected $_registry;

    protected $planResource;

    protected $planCollectionFactory;

    protected $collection;

    protected $helper;

    protected $_plan;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Omnyfy\VendorSubscription\Model\Resource\Plan $planResource,
        \Omnyfy\VendorSubscription\Model\Resource\Plan\CollectionFactory $planCollectionFactory,
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        array $data = [])
    {
        $this->_registry = $registry;
        $this->planResource = $planResource;
        $this->planCollectionFactory = $planCollectionFactory;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getVendorType() {
        return $this->_registry->registry('current_omnyfy_vendor_type');
    }
	
	 public function getVendorTypeId(){
        return $this->getRequest()->getParam('type_id', null);
    }

    public function getPlan() {
        $plan = $this->_registry->registry('current_omnyfy_plan');
        if (!empty($plan)) {
            return $plan;
        }

        $planCollection = $this->getPlanCollection();
        if (1==$planCollection->getSize()) {
            $plan = $planCollection->getFirstItem();
            $this->_registry->register('current_omnyfy_plan', $plan);
            return $plan;
        }

        return false;
    }

    public function getPlanRoleId() {
        $plan = $this->getPlan();
        if (empty($plan)) {
            return false;
        }

        $map = $this->getRoleIdsMap();
        if (array_key_exists($plan->getId(), $map)) {
            return $plan->getId() . '_' . $map[$plan->getId()];
        }

        return false;
    }

    public function getPlanCollection() {
        if (null == $this->collection) {
            $vendorType = $this->getVendorType();
            $collection = $this->planCollectionFactory->create();
            $collection->addRoleIdJoin($this->getVendorTypeId());

            $this->collection = $collection;
        }

        return $this->collection;
    }

    public function getRoleIdsMap() {
        $vendorType = $this->getVendorType();
        return $this->planResource->getRoleIdMapByVendorTypeId($this->getVendorTypeId());
    }

    public function isAllFree() {
        $collection = $this->getPlanCollection();

        foreach($collection as $plan) {
            if (!$plan->getIsFree()) {
                return false;
            }
        }

        return true;
    }

    public function isNoFree() {
        $collection = $this->getPlanCollection();

        foreach($collection as $plan) {
            if ($plan->getIsFree()) {
                return false;
            }
        }

        return true;
    }

    public function formatPrice($amount) {
        return $this->helper->formatPrice($amount);
    }
}
 