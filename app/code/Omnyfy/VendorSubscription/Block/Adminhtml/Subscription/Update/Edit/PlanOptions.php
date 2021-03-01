<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-09-16
 * Time: 00:02
 */
namespace Omnyfy\VendorSubscription\Block\Adminhtml\Subscription\Update\Edit;

class PlanOptions extends \Magento\Backend\Block\Template
{
    protected $_template = 'plan_options.phtml';

    protected $helper;

    protected $coreRegistry;

    protected $intervalSource;

    protected $priceCurrency;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        \Omnyfy\VendorSubscription\Model\Source\Interval $intervalSource,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        array $data = [])
    {
        $this->helper = $helper;
        $this->coreRegistry = $coreRegistry;
        $this->intervalSource = $intervalSource;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }

    public function formatPrice($price)
    {
        return $this->priceCurrency->format($price, true, 2);
    }

    public function parseInterval($plan)
    {
        $values = $this->intervalSource->toValuesArray();
        return array_key_exists($plan->getInterval(), $values) ? $values[$plan->getInterval()] : '';
    }

    public function parseBenefits($plan)
    {
        $benefits = $plan->getBenefitsArray();
        $result = '';
        foreach($benefits as $str) {
            $result .= '<p>' . $str . '</p>';
        }

        return $result;
    }

    public function isSelected($plan)
    {
        if (empty($plan)) {
            return false;
        }
        $subscription = $this->coreRegistry->registry('current_omnyfy_subscription_subscription');
        return ($subscription->getPlanId() == $plan->getId()) ? true : false;
    }

    public function getPlans()
    {
        $subscription = $this->coreRegistry->registry('current_omnyfy_subscription_subscription');
        $vendorTypeId = $subscription->getVendorTypeId();
        $planId = $subscription->getPlanId();
        return $this->helper->getUpdatePlans($vendorTypeId, $planId);
    }
}