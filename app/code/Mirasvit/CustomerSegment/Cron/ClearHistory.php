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



namespace Mirasvit\CustomerSegment\Cron;


use Magento\Framework\App\ResourceConnection;

class ClearHistory
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * ClearHistory constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Remove 30 days old segment history from database
     */
    public function execute()
    {
        $monthAgo = date('Y-m-d H:i:s', time() - 30 * 24 * 60 * 60);
        $adapter = $this->resourceConnection->getConnection();

        $query = $adapter->select()->from(
                ['history' => $this->resourceConnection->getTableName('mst_customersegment_segment_history')]
            )
            ->where('created_at < ?', $monthAgo)
            ->deleteFromSelect('history');

        $adapter->query($query);
    }
}