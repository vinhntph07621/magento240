<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   2.1.18
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Api\Service\Item\ItemManagement;


interface QuantityInterface
{
    /**
     * @param int $productId
     * @return float
     */
    public function getQtyStock($productId);

    /**
     * @param \Mirasvit\Rma\Api\Data\ItemInterface $item
     * @return int
     */
    public function getQtyOrdered(\Mirasvit\Rma\Api\Data\ItemInterface $item);

    /**
     * Returns quantity, available for return.
     * @param \Mirasvit\Rma\Api\Data\ItemInterface $item
     * @return int
     */
    public function getQtyAvailable(\Mirasvit\Rma\Api\Data\ItemInterface $item);

    /**
     * @param \Mirasvit\Rma\Api\Data\ItemInterface $item
     * @return int
     */
    public function getItemQtyReturned(\Mirasvit\Rma\Api\Data\ItemInterface $item);

    /**
     * @param \Mirasvit\Rma\Api\Data\ItemInterface $orderItem
     *
     * @return int
     */
    public function getQtyInRma($orderItem);
}