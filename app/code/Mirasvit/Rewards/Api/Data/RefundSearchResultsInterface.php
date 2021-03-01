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
 * @package   mirasvit/module-rewards
 * @version   3.0.21
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Api\Data;

/**
 * Interface for refund search results.
 */
interface RefundSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get transactions list.
     *
     * @return \Mirasvit\Rewards\Api\Data\RefundInterface[]
     */
    public function getItems();

    /**
     * Set tiers list.
     *
     * @param array $items Array of \Mirasvit\Rewards\Api\Data\RefundInterface[]
     * @return $this
     */
    public function setItems(array $items);
}
