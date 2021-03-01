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
use Mirasvit\CustomerSegment\Api\Service\Segment\Condition\CollectionProviderInterface;

/**
 * Class WishlistProvider, provides product collection associated with specific customer.
 * Can process only Registered Customers
 */
class WishlistProvider extends AbstractCollectionProvider implements CollectionProviderInterface
{
    /**
     * @param AbstractModel $candidate
     *
     * @inheritDoc
     */
    protected function getCollectionSelect(AbstractModel $candidate)
    {
        $select = $this->adapter->select();
        $select->from(['wishlist' => $this->resourceConnection->getTableName('wishlist')], [])
            ->joinInner(
                ['wishlist_item' => $this->resourceConnection->getTableName('wishlist_item')],
                'wishlist.wishlist_id = wishlist_item.wishlist_id',
                ['product_id']
            )
            ->where('wishlist.customer_id = ?', $candidate->getCustomerId())
            ->where('wishlist_item.store_id = ?', $candidate->getStoreId());

        return $select;
    }

    /**
     * @inheritDoc
     */
    protected function getDateField()
    {
        return 'quote_item.created_at';
    }

    /**
     * @param AbstractModel $candidate
     *
     * @inheritDoc
     */
    public function canProcessCandidate(AbstractModel $candidate)
    {
        return (bool) $candidate->getCustomerId();
    }
}
