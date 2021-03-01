<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2/10/19
 * Time: 5:31 pm
 */
namespace Omnyfy\VendorSubscription\Helper;

use Omnyfy\VendorSubscription\Model\Config;
use Magento\Framework\App\Area;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $emailHelper;

    protected $priceHelper;

    protected $_storeManager;

    protected $intervalSource;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Core\Helper\Email $emailHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Omnyfy\VendorSubscription\Model\Source\Interval $intervalSource
    ) {
        $this->_storeManager = $storeManager;
        $this->emailHelper = $emailHelper;
        $this->priceHelper = $priceHelper;
        $this->intervalSource = $intervalSource;
        parent::__construct($context);
    }

    public function sendCancelEmailToAdmin($subscription)
    {
        $templateId = $this->scopeConfig->getValue(Config::CANCEL_SUBSCRIPTION_ADMIN_TEMPLATE);
        $vars = [
            'vendor_name' => $subscription->getVendorName(),
            'end_date' => $this->parseDate($subscription->getExpiryAt()),
            'plan_name' => $subscription->getPlanName(),
            'plan_price' => $this->parsePrice($subscription->getPlanPrice()),
            'plan_description' => $subscription->getDescription(),
            'billing_interval' => $this->parseInterval($subscription->getBillingInterval())
        ];

        $from = 'general';
        $to = [
            'email' => $this->scopeConfig->getValue(Config::XML_PATH_ADMIN_EMAIL),
            'name' => $this->scopeConfig->getValue(Config::XML_PATH_ADMIN_NAME)
        ];
        $storeId = $this->_storeManager->getStore()->getId();

        $this->emailHelper->sendEmail($templateId, $vars, $from, $to, Area::AREA_ADMINHTML, $storeId);
    }

    public function sendCancelEmailToVendor($subscription)
    {
        $templateId = $this->scopeConfig->getValue(Config::CANCEL_SUBSCRIPTION_VENDOR_TEMPLATE);
        $store = $this->_storeManager->getStore();
        $vars = [
            'vendor_name' => $subscription->getVendorName(),
            'end_date' => $this->parseDate($subscription->getExpiryAt()),
            'plan_name' => $subscription->getPlanName(),
            'plan_price' => $this->parsePrice($subscription->getPlanPrice()),
            'plan_description' => $subscription->getDescription(),
            'billing_interval' => $this->parseInterval($subscription->getBillingInterval()),
            'store' => $store
        ];

        $from = 'general';
        $to = [
            'email' => $subscription->getVendorEmail(),
            'name' => $subscription->getVendorName()
        ];
        $storeId = $store->getId();

        $this->emailHelper->sendEmail($templateId, $vars, $from, $to,Area::AREA_ADMINHTML, $storeId);
    }

    public function sendCancelEmails($subscription)
    {
        $this->sendCancelEmailToVendor($subscription);

        $this->sendCancelEmailToAdmin($subscription);
    }

    public function parseInterval($interval)
    {
        $map = $this->intervalSource->toValuesArray();
        if (array_key_exists($interval, $map)) {
            return $map[$interval];
        }

        return '';
    }

    public function parseDate($date)
    {
        return date('d/M/Y', strtotime($date));
    }

    public function parsePrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }
}
 