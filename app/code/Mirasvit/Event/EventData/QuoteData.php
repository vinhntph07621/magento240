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



namespace Mirasvit\Event\EventData;

use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote;
use Mirasvit\Event\Api\Data\EventDataInterface;
use Mirasvit\Event\EventData\Condition\QuoteCondition;

class QuoteData extends Quote implements EventDataInterface
{
    const IDENTIFIER = 'quote';

    const ID = 'quote_id';

    /**
     * @param string $class
     * @return mixed
     */
    public function get($class)
    {
        $om = ObjectManager::getInstance();
        return $om->create($class);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionClass()
    {
        return QuoteCondition::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Quote');
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        $attributes = [
            'subtotal'    => [
                'label' => __('Subtotal'),
                'type'  => self::ATTRIBUTE_TYPE_NUMBER,
            ],
        ];

        return $attributes;
    }
}
