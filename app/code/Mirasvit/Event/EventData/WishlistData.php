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

use Magento\Wishlist\Model\Wishlist;
use Mirasvit\Event\Api\Data\EventDataInterface;
use Mirasvit\Event\EventData\Condition\WishlistCondition;

class WishlistData extends Wishlist implements EventDataInterface
{
    use ContextTrait;

    const ID = 'wishlist_id';

    const IDENTIFIER = 'wishlist';

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    /**
     * @return string
     */
    public function getConditionClass()
    {
        return WishlistCondition::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Wishlist');
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [];
    }
}
