<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-17
 * Time: 12:33
 */
namespace Omnyfy\Stripe\Helper;

use Magento\Framework\App\Helper\Context;

class Gateway extends \Magento\Framework\App\Helper\AbstractHelper implements \Omnyfy\VendorSignUp\Helper\GatewayInterface
{
    /**
     * @var \StripeIntegration\Payments\Model\Config
     */
    private $stripeConfig;

    /**
     * @var \StripeIntegration\Payments\Helper\Generic
     */
    private $stripeHelper;

    /**
     * @var \Omnyfy\Stripe\Model\ResourceModel\VendorConnectAccount
     */
    private $vendorConnectAccount;

    /**
     * @var null|array
     */
    private $userData = null;

    /**
     * Gateway constructor.
     * @param Context $context
     * @param \StripeIntegration\Payments\Model\Config $stripeConfig
     * @param \StripeIntegration\Payments\Helper\Generic $stripeHelper
     * @param \Omnyfy\Stripe\Model\ResourceModel\VendorConnectAccount $vendorConnectAccount
     */
    public function __construct(
        Context $context,
        \StripeIntegration\Payments\Model\Config $stripeConfig,
        \StripeIntegration\Payments\Helper\Generic $stripeHelper,
        \Omnyfy\Stripe\Model\ResourceModel\VendorConnectAccount $vendorConnectAccount
    ) {
        $this->stripeConfig = $stripeConfig;
        $this->stripeHelper = $stripeHelper;
        $this->vendorConnectAccount = $vendorConnectAccount;
        \Stripe\Stripe::setApiKey($this->stripeConfig->getSecretKey());
        \Stripe\Stripe::setApiVersion("2019-08-14");
        parent::__construct($context);
    }

    /**
     * @param array $data
     * @return \Stripe\Transfer
     */
    public function createTransfer($data)
    {
        return \Stripe\Transfer::create($data);
    }

    /**
     * @param string $token
     * @return mixed|null
     */
    public function getStripeAccountId($token)
    {
        $response = \Stripe\OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => $token,
        ]);

        if (!empty($response->stripe_user_id)) {
            return $response->stripe_user_id;
        }
        return null;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param array $params
     * @return \Stripe\Charge
     */
    public function updateChargeByOrder($order, $params)
    {
        $token = $order->getPayment()->getTransactionId();
        if (empty($token)) {
            $token = $order->getPayment()->getLastTransId();
        }

        if ($token) {
            $token = $this->stripeHelper->cleanToken($token);
            if (strpos($token, 'pi_') === 0)
            {
                $pi = \Stripe\PaymentIntent::update($token, $params);
                $charge = $pi->charges->data[0];
            }
            else
            {
                $charge = \Stripe\Charge::update($token, $params);
            }
        }
        return $charge;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param array $params
     * @return \Stripe\Charge | null
     */
    public function retrieveChargeByOrder($order)
    {
        $token = $order->getPayment()->getTransactionId();
        if (empty($token)) {
            $token = $order->getPayment()->getLastTransId();
        }

        if ($token) {
            $token = $this->stripeHelper->cleanToken($token);
            if (strpos($token, 'pi_') === 0)
            {
                $pi = \Stripe\PaymentIntent::retrieve($token);
                $charge = $pi->charges->data[0];
            }
            else
            {
                $charge = \Stripe\Charge::retrieve($token);
            }
            return $charge;
        }
        return null;
    }

    /**
     * @param string $transferId
     * @param float $amount
     * @return \Stripe\TransferReversal
     */
    public function reversingTransfer($transferId, $amount)
    {
        return \Stripe\Transfer::createReversal(
            $transferId,
            [
                'amount' => $amount
            ]
        );
    }

    /**
     * @param array $data
     * @return array
     */
    public function createUser($data)
    {
        return [];
    }

    /**
     * @param array $data
     * @param string $userId
     * @return array
     */
    public function updateUser($data, $userId)
    {
        return [];
    }

    /**
     * @param array $data
     * @return array
     */
    public function createCompany($data)
    {
        return [];
    }

    /**
     * @param array $data
     * @return array
     */
    public function updateCompany($data)
    {
        return [];
    }

    /**
     * @param array $data
     * @return array
     */
    public function createBankAccount($data)
    {
        return [];
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUserById($userId)
    {
        if ($this->userData === null) {
            $this->userData = \Stripe\Account::retrieve($userId)->jsonSerialize();
        }
        return $this->userData;
    }

    /**
     * @param string $url
     * @return array
     * @deprecated Use specified method instead
     */
    public function getAssemblyDetails($url)
    {
        return [];
    }

    /**
     * @param string $email
     * @return array
     */
    public function searchUser($email)
    {
        $userId = $this->vendorConnectAccount->getStripeAccountIdByEmail($email);
        if ($this->isUserExist($email)) {
            return $this->getUserById($userId);
        }
        return ['errors' => 'User is not exist'];
    }

    /**
     * @param string $email
     * @return boolean
     */
    public function isUserExist($email)
    {
        $userId = $this->vendorConnectAccount->getStripeAccountIdByEmail($email);
        if (!empty($userId)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $userId
     * @return array
     */
    public function getBankAccountByUserId($userId)
    {
        $accountData = $this->getUserById($userId);
        if ($accountData['external_accounts']['data'][0]) {
            return $accountData['external_accounts']['data'][0];
        }
        return [];
    }

    /**
     * @param string $userId
     * @return mixed
     */
    public function getWalletByUserId($userId)
    {
        return false;
    }

    /**
     * @param array $data
     * @return string|boolean
     */
    public function readBankAccountId($data)
    {
        return false;
    }

    /**
     * @param array $data
     * @return string|boolean
     */
    public function readUserId($data)
    {
        return false;
    }

    /**
     * @param array $data
     * @return string|boolean
     */
    public function readUserStatus($data)
    {
        return false;
    }

    /**
     * @param array $data
     * @return string|boolean
     */
    public function readCompanyId($data)
    {
        return false;
    }

    /**
     * @param array $data
     * @return string|boolean
     */
    public function readWalletId($data)
    {
        return false;
    }

    /**
     * Get Stripe connect url for vendor
     *
     * @param string $stripeAccountCode
     * @return string
     */
    public function getAccountLoginLink($stripeAccountCode)
    {
        $result = \Stripe\Account::createLoginLink($stripeAccountCode);
        return $result->url;
    }

    /**
     * @param array $data
     * @param array $stripeAccount
     * @return \Stripe\Payout
     */
    public function createPayout($data, $stripeAccount) {
        return \Stripe\Payout::create($data, $stripeAccount);
    }
}
