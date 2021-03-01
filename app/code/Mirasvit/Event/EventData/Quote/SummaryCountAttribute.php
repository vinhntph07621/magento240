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
 * @package   mirasvit/module-event
 * @version   1.2.36
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Event\EventData\Quote;


use Magento\Framework\Model\AbstractModel;
use Mirasvit\Event\Api\Data\AttributeInterface;
use Mirasvit\Event\Api\Data\EventDataInterface;
use Mirasvit\Event\EventData\Condition\QuoteCondition;
use Mirasvit\Event\EventData\QuoteData;

class SummaryCountAttribute implements AttributeInterface
{
    const ATTR_CODE  = 'summary_count';
    const ATTR_LABEL = 'Total count of products';

    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        return self::ATTR_CODE;
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel()
    {
        return __(self::ATTR_LABEL);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getType()
    {
        return EventDataInterface::ATTRIBUTE_TYPE_NUMBER;
    }

    /**
     * Calculate total count of products in quote.
     *
     * {@inheritDoc}
     */
    public function getValue(AbstractModel $dataObject)
    {
        /** @var QuoteData $quote */
        $quote = $dataObject->getData(QuoteData::IDENTIFIER);

        return $quote->getItemsCount();
    }

    /**
     * {@inheritDoc}
     */
    public function getConditionClass()
    {
        return QuoteCondition::class . '|' . self::ATTR_CODE;
    }
}
