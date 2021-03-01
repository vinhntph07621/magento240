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



namespace Mirasvit\CustomerSegment\Api\Service\Segment\Condition;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterface;
use Mirasvit\CustomerSegment\Model\Segment\Condition\Daterange;

/**
 * ValueProviderInterface provides value option to the segment conditions.
 */
interface ValueProviderInterface
{
    /**
     * Get value option code.
     *
     * @return string
     */
    public function getCode();

    /**
     * Get value option label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Method used to check whether concrete provider can process this candidate.
     * Some product collection providers work only with registered customers, another with both.
     *
     * @param AbstractModel $candidate
     *
     * @return bool
     */
    public function canProcessCandidate(AbstractModel $candidate);

    /**
     * @param AbstractModel $candidate
     * @param DateRange $dateRange
     *
     * @return AbstractModel[]
     */
    public function provideCollection(AbstractModel $candidate, Daterange $dateRange = null);
}
