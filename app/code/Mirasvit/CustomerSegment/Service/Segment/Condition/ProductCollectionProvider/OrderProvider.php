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



namespace Mirasvit\CustomerSegment\Service\Segment\Condition\ProductCollectionProvider;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterface;

class OrderProvider extends AbstractCollectionProvider
{
    /**
     * @param AbstractModel $candidate
     *
     * @inheritDoc
     */
    protected function getCollectionSelect(AbstractModel $candidate)
    {
        $select = $this->adapter->select()
            ->from(['sales_order' => $this->resourceConnection->getTableName('sales_order')], [])
            ->joinInner(
                ['order_item' => $this->resourceConnection->getTableName('sales_order_item')],
                'sales_order.entity_id = order_item.order_id',
                ['order_item.product_id']
            )
            ->where('sales_order.store_id = ?', $candidate->getStoreId());

        $this->filterByCustomer($select, $candidate, 'sales_order.customer_id', 'sales_order.customer_email');

        return $select;
    }

    /**
     * @inheritDoc
     */
    protected function getDateField()
    {
        return 'order_item.created_at';
    }
}
