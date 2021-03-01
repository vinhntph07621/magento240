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

class ViewProvider extends AbstractCollectionProvider
{
    /**
     * @param AbstractModel $candidate
     *
     * @inheritDoc
     */
    protected function getCollectionSelect(AbstractModel $candidate)
    {
        $select = $this->adapter->select()
            ->from(['view' => $this->resourceConnection->getTableName('report_viewed_product_index')], ['product_id'])
            ->where('customer_id = ?', $candidate->getCustomerId())
            ->where('store_id = ?', $candidate->getStoreId());

        return $select;
    }

    /**
     * @inheritDoc
     */
    protected function getDateField()
    {
        return 'added_at';
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
