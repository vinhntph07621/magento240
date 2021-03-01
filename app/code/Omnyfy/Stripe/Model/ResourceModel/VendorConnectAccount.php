<?php
namespace Omnyfy\Stripe\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;

class VendorConnectAccount extends AbstractEntity
{
    /**
     * @param int $vendorId
     * @return string
     */
    public function getStripeAccountIdByVendorId($vendorId) {
        $conn = $this->getConnection();
        $select = $conn->select()->from(
            ['main_table' => $this->getTable('omnyfy_vendor_vendor_entity_varchar')],
            'value'
        )
            ->join(
                ['eav' => $this->getTable('eav_attribute')],
                'main_table.attribute_id = eav.attribute_id'
            )
            ->where("main_table.entity_id = ?", $vendorId)
            ->where('eav.attribute_code = ?', 'stripe_account_code');

        return $conn->fetchOne($select);
    }

    /**
     * @param string $stripeAccountId
     * @return string
     */
    public function getVendorIdByStripeAccountId($stripeAccountId) {
        $conn = $this->getConnection();
        $select = $conn->select()->from(
            ['main_table' => $this->getTable('omnyfy_vendor_vendor_entity_varchar')],
            'entity_id'
        )
            ->join(
                ['eav' => $this->getTable('eav_attribute')],
                'main_table.attribute_id = eav.attribute_id'
            )
            ->where("main_table.value = ?", $stripeAccountId)
            ->where('eav.attribute_code = ?', 'stripe_account_code');

        return $conn->fetchOne($select);
    }

    /**
     * @param array $vendorOrderIds
     * @return array
     */
    public function getOrderIdFromVendorOrders($vendorOrderIds) {
        $conn = $this->getConnection();
        $select = $conn->select()->from(
            ['main_table' => $this->getTable('omnyfy_mcm_vendor_order')],
            'order_id'
        )->where("main_table.id in (?)", $vendorOrderIds);

        return $conn->fetchCol($select);
    }

    /**
     * @return string
     */
    protected function getStripeAccountIdAttributeCode()
    {
        $conn = $this->getConnection();
        $select = $conn->select()->from(
            ['main_table' => $this->getTable('eav_attribute')],
            'attribute_id'
        )->where("main_table.attribute_code = ?", 'stripe_account_code');

        return $conn->fetchOne($select);
    }

    /**
     * @param string $email
     * @return string
     */
    public function getStripeAccountIdByEmail($email)
    {
        $conn = $this->getConnection();

        $select = $conn->select()->from(
            ['main_table' => $this->getTable('omnyfy_vendor_vendor_entity')],
            []
        )->join(
            ['vendor_varchar' => $this->getTable('omnyfy_vendor_vendor_entity_varchar')],
            'main_table.entity_id = vendor_varchar.entity_id',
            'value'
        )->where(
            "main_table.email = ?",
            $email
        )->where('vendor_varchar.attribute_id = ?', $this->getStripeAccountIdAttributeCode());
        return $conn->fetchOne($select);
    }

    /**
     * @param int $vendorId
     * @param int $orderId
     * @return string
     */
    public function getVendorOrderId($orderId, $vendorId) {
        $conn = $this->getConnection();
        $select = $conn->select()->from(
            ['main_table' => $this->getTable('omnyfy_mcm_vendor_order')],
            'id'
        )->where("main_table.order_id = ?", $orderId
        )->where("main_table.vendor_id = ?", $vendorId);

        return $conn->fetchOne($select);
    }

    /**
     * @param int $vendorId
     * @param int $orderId
     * @return float
     */
    public function getPayoutAmountByOrderVendor($orderId, $vendorId)
    {
        $vendorOrderId = $this->getVendorOrderId($orderId, $vendorId);
        $conn = $this->getConnection();
        $selectPayountAmount = $conn->select()->from(
            ['main_table' => $this->getTable('omnyfy_mcm_vendor_payout_history')],
            'payout_amount'
        )->where("main_table.vendor_order_id = ?", $vendorOrderId
        )->where("main_table.vendor_id = ?", $vendorId);
        return (float) $conn->fetchOne($selectPayountAmount);
    }

    /**
     * @param string $vendorId
     * @param array $accountInfo
     */
    public function updateVendorPayout($vendorId, $accountInfo)
    {
        $conn = $this->getConnection();
        $conn->update(
            $this->getTable('omnyfy_mcm_vendor_payout'),
            [
                'account_ref' => $accountInfo['stripe_account_id'],
                'ewallet_id' => $accountInfo['stripe_account_id'],
                'third_party_account_id' => $accountInfo['bank_account_id']
            ],
            ['vendor_id = ?' => $vendorId]
        );
    }

    /**
     * @param string $stripePayoutId
     * @param array $extInfo
     */
    public function updateVendorWithdrawal($stripePayoutId, $extInfo)
    {
        $conn = $this->getConnection();
        $conn->insert(
            $this->getTable('omnyfy_stripe_withdrawals_webhooks_data'),
            [
                'stripe_payout_id' => $stripePayoutId,
                'payout_ext_info' => json_encode($extInfo)
            ]
        );
    }

    /**
     * @param string $stripePayoutId
     * @return string
     */
    public function getExtInfoByStripePayout($stripePayoutId)
    {
        $conn = $this->getConnection();
        $select = $conn->select()->from(
            ['main_table' => $this->getTable('omnyfy_stripe_withdrawals_webhooks_data')],
            'payout_ext_info'
        )->where("main_table.stripe_payout_id = ?", $stripePayoutId);
        return $conn->fetchOne($select);
    }
}
