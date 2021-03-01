<?php
/**
 * Project: Filter RMA Grid collection based on Vendor Id.
 * Author: seth
 * Date: 10/2/20
 * Time: 2:26 pm
 **/

namespace Omnyfy\Rma\Model\ResourceModel\Rma\Grid;

use Mirasvit\Rma\Api\Data\RmaInterface;

/**
 * Class Collection
 * @package Omnyfy\Rma\Model\ResourceModel\Rma\Grid
 */
class Collection extends \Mirasvit\Rma\Model\ResourceModel\Rma\Grid\Collection {

    /**
     * @var \Omnyfy\Rma\Helper\Data
     */
    protected $helper;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omnyfy\Rma\Helper\Data $helper
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Rma\Helper\Data $helper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->helper = $helper;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $connection,
            $resource
        );
    }

    /**
     * Added vendor id in filtering the Grid collection.
     */
    protected function initFields()
    {
        /* @noinspection PhpUnusedLocalVariableInspection */
        $select = $this->getSelect();
        $select->joinLeft(
            ['rma_item' => $this->getTable('mst_rma_item')],
            'main_table.rma_id = rma_item.rma_id',
            ['vendor_id' => 'rma_item.vendor_id']
        );

        if ($vendorId = $this->helper->getVendorId()) {
            $select->where("rma_item.vendor_id = $vendorId");
        }

        $select->group('main_table.rma_id')->order('main_table.rma_id DESC');
        $select->columns(['name' => new \Zend_Db_Expr("CONCAT(main_table.firstname, ' ', main_table.lastname)")]);
    }
}