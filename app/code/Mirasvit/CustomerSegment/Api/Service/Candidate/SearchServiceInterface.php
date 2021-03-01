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



namespace Mirasvit\CustomerSegment\Api\Service\Candidate;


use Magento\Framework\Api\SearchResults;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;

interface SearchServiceInterface
{
    /**
     * Retrieve candidates which match a specified criteria.
     *
     * This call returns an array of objects.
     *
     * @param SegmentInterface $segment
     *
     * @return SearchResultsInterface|SearchResults
     */
    public function getList(SegmentInterface $segment);
}
