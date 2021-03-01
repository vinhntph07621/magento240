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



namespace Mirasvit\CustomerSegment\Api\Repository\Candidate;


use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\FinderInterface;

interface FinderRepositoryInterface
{
    /**
     * @param SegmentInterface $segment
     *
     * @return \Mirasvit\CustomerSegment\Api\Service\Candidate\FinderInterface[]
     */
    public function getList(SegmentInterface $segment = null);

    /**
     * Get finder by name.
     *
     * @param string $code
     *
     * @return null|FinderInterface
     */
    public function getByCode($code);
}