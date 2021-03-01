<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Api;

/**
 * @api
 */
interface CheckoutDataRepositoryInterface
{
    /**
     * Save Data from Frontend Checkout
     *
     * @param int $amastyCartId
     * @param string $checkoutFormCode
     * @param string $shippingMethodCode
     * @param \Amasty\Orderattr\Api\Data\EntityDataInterface $entityData
     * @throws \Magento\Framework\Exception\InputException
     *
     * @return \Amasty\Orderattr\Api\Data\EntityDataInterface
     */
    public function save(
        $amastyCartId,
        $checkoutFormCode,
        $shippingMethodCode,
        \Amasty\Orderattr\Api\Data\EntityDataInterface $entityData
    );
}
