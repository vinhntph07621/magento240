<?php
namespace Omnyfy\Stripe\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use StripeIntegration\Payments\Model\Config as StripePaymentConfig;
use Magento\Framework\Encryption\EncryptorInterface;

class GetConfigData extends AbstractHelper
{
    const XML_PATH_STRIPE_CONFIG = 'stripe_config/';

    protected $stripePaymentConfig;

    protected $encryptor;

    public function __construct(
        Context $context,
        StripePaymentConfig $stripePaymentConfig,
        EncryptorInterface $encryptor
    ) {
        parent::__construct($context);
        $this->stripePaymentConfig = $stripePaymentConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * @param string $field
     * @param null|int $storeId
     * @return string
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    /**
     * @param string $code
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_STRIPE_CONFIG . 'general/' . $code, $storeId);
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->getGeneralConfig('client_id');
    }

    /**
     * @return string
     */
    public function getConnectAccountWebhooksSigningSecret()
    {
        $key = $this->getGeneralConfig('connect_account_webhook_siging_secret');

        // The following is due to a magento bug causing the key to need to be saved more than once to be decrypted correctly
        if (!preg_match('/^[A-Za-z0-9_]+$/',$key))
            $key = $this->encryptor->decrypt($key);

        return trim($key);
    }
}
