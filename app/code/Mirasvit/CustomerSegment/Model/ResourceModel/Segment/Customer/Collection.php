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



namespace Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Customer;


use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface;
use Magento\Backend\Block\Widget\Grid\Column;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = CustomerInterface::ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            'Mirasvit\CustomerSegment\Model\Segment\Customer',
            'Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Customer'
        );
    }

    /**
     * Callback function that filters collection by field "name" from grid.
     *
     * @param AbstractCollection $collection
     * @param Column $column
     * @return void
     */
    public function addCustomerNameFilterCallback($collection, $column)
    {
        $names = explode(' ', $column->getFilter()->getData('value'));
        foreach ($names as $name) {
            $collection->getSelect()
                ->where('CONCAT(IFNULL(customer.firstname, order.customer_firstname)," "'
                    . ', IFNULL(customer.lastname, order.customer_lastname)) LIKE ?'
                    , '%' . $name . '%'
                );
        }
    }

    /**
     * Callback function that filters collection by field "type" from grid.
     *
     * @param AbstractCollection $collection
     * @param Column $column
     * @return void
     */
    public function addCustomerTypeFilterCallback($collection, $column)
    {
        if ($column->getFilter()->getData('value') == SegmentInterface::TYPE_CUSTOMER) {
            $collection->getSelect()->where('customer.entity_id IS NOT NULL');
        } else {
            $collection->getSelect()->where('customer.entity_id IS NULL');
        }
    }
}
