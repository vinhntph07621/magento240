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



namespace Mirasvit\CustomerSegment\Repository\Candidate;


use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\Candidate\FinderRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\FinderInterface;

class FinderRepository implements FinderRepositoryInterface
{
    /**
     * @var array|FinderInterface[]
     */
    private $candidateFinders;

    /**
     * @var array|FinderInterface[]
     */
    private $finderRegistry;

    /**
     * @param FinderInterface[] $candidateFinders
     */
    public function __construct($candidateFinders = [])
    {
        $this->candidateFinders = $candidateFinders;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SegmentInterface $segment = null)
    {
        $finders = [];

        if ($segment) {
            foreach ($this->candidateFinders as $finder) {
                if ($finder->canProcess($segment->getType())) {
                    $finders[] = $finder;
                }
            }

            return $finders;
        }

        return $this->candidateFinders;
    }

    /**
     * {@inheritdoc}
     */
    public function getByCode($code)
    {
        if (isset($this->finderRegistry[$code])) {
            return $this->finderRegistry[$code];
        }

        foreach ($this->getList() as $finder) {
            if ($finder->getCode() === $code) {
                $this->finderRegistry[$code] = $finder;

                return $finder;
            }
        }

        return null;
    }
}