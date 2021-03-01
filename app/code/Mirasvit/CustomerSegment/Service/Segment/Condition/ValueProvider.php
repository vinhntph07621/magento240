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



namespace Mirasvit\CustomerSegment\Service\Segment\Condition;


use Magento\Framework\Model\AbstractModel;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\Condition\ValueProviderInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\Condition\CollectionProviderInterface;
use Mirasvit\CustomerSegment\Model\Segment\Condition\Daterange;

class ValueProvider implements ValueProviderInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $label;

    /**
     * @var CollectionProviderInterface
     */
    private $collectionProvider;

    /**
     * ValueProviderInterface constructor.
     *
     * @param string $code
     * @param string $label
     * @param CollectionProviderInterface $collectionProvider
     */
    public function __construct($code, $label, CollectionProviderInterface $collectionProvider = null)
    {
        $this->code = $code;
        $this->label = $label;
        $this->collectionProvider = $collectionProvider;
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @inheritDoc
     */
    public function canProcessCandidate(AbstractModel $candidate)
    {
        return $this->collectionProvider->canProcessCandidate($candidate);
    }

    /**
     * @inheritDoc
     */
    public function provideCollection(AbstractModel $candidate,  Daterange $dateRange = null)
    {
        return $this->collectionProvider->provideCollection($candidate, $dateRange);
    }
}