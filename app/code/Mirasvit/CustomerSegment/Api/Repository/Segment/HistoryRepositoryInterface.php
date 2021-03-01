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



namespace Mirasvit\CustomerSegment\Api\Repository\Segment;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;

interface HistoryRepositoryInterface
{
    /**
     * Retrieve history.
     *
     * @param int $id
     *
     * @return HistoryInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * Create or update a history.
     *
     * @param HistoryInterface $history
     *
     * @return HistoryInterface
     */
    public function save(HistoryInterface $history);

    /**
     * Delete history.
     *
     * @param HistoryInterface $history
     *
     * @return bool true on success
     */
    public function delete(HistoryInterface $history);

    /**
     * Retrieve collection of histories.
     *
     * @return \Mirasvit\CustomerSegment\Model\ResourceModel\Segment\History\Collection
     */
    public function getCollection();
}
