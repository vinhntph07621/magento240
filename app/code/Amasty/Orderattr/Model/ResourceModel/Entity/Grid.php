<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Orderattr
 */


namespace Amasty\Orderattr\Model\ResourceModel\Entity;

use Amasty\Orderattr\Api\Data\CheckoutEntityInterface;

class Grid extends \Amasty\Orderattr\Model\ResourceModel\Entity\EntityData\Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addFieldToFilter(
            CheckoutEntityInterface::PARENT_ENTITY_TYPE,
            CheckoutEntityInterface::ENTITY_TYPE_ORDER
        );
        $this->getSelect()->group($this->getIdFieldName());
    }
}
