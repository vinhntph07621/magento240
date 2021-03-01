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



namespace Mirasvit\CustomerSegment\Api\Service\Segment;

use Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Customer\Collection as SegmentCustomerCollection;

interface CustomerDataProviderInterface
{
    /**
     * Join customers to segment customer collection
     *
     * @param SegmentCustomerCollection $collection
     *
     * @return mixed
     */
    public function provideCustomerInfo(SegmentCustomerCollection $collection);

    /**
     * Retrieve amount of unique customers from all segments.
     *
     * @return int
     */
    public function countUniqueCustomers();
}
