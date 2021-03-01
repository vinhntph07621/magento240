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
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Api\Data\Segment;


use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for customer segments search results.
 *
 * @api
 */
interface CustomerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get segment customers list.
     *
     * @return \Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface[]
     */
    public function getItems();

    /**
     * Set segment customers list.
     *
     * @param \Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}