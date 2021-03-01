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

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Model\AbstractModel;
use Mirasvit\CustomerSegment\Api\Data\CandidateInterface;
use Mirasvit\CustomerSegment\Api\Service\Segment\Condition\CollectionProviderInterface;

class CartProvider extends AbstractCollectionProvider implements CollectionProviderInterface
{
    /**
     * @param AbstractModel $candidate
     *
     * @inheritDoc
     */
    protected function getCollectionSelect(AbstractModel $candidate)
    {
        $select = $this->adapter->select();
        $select->from(['quote' => $this->resourceConnection->getTableName('quote')], [])
            ->joinInner(
                ['quote_item' => $this->resourceConnection->getTableName('quote_item')],
                'quote.entity_id = quote_item.quote_id',
                ['product_id']
            )
            ->where('quote.is_active = 1') // Only active quote considered
            ->where('quote.store_id = ?', $candidate->getStoreId());

        $this->filterByCustomer($select, $candidate, 'quote.customer_id', 'quote.customer_email');

        return $select;
    }

    /**
     * @inheritDoc
     */
    protected function getDateField()
    {
        return 'quote_item.created_at';
    }
}
