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

class GiftRegistryProvider extends AbstractCollectionProvider
{
    /**
     * @param AbstractModel $candidate
     *
     * @inheritDoc
     */
    protected function getCollectionSelect(AbstractModel $candidate)
    {
        $select = $this->adapter->select()
            ->from(['giftr' => $this->resourceConnection->getTableName('mst_giftr_registry')], [])
            ->joinInner(
                ['giftr_item' => $this->resourceConnection->getTableName('mst_giftr_item')],
                'giftr.registry_id = giftr_item.registry_id',
                ['giftr_item.product_id']
            )
            ->where('giftr_item.store_id = ?', $candidate->getStoreId())
            ->where('giftr.customer_id = ?', $candidate->getCustomerId());

        return $select;
    }

    /**
     * @inheritDoc
     */
    protected function getDateField()
    {
        return 'giftr_item.created_at';
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
