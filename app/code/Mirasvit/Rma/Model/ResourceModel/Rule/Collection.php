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
 * @package   mirasvit/module-rma
 * @version   2.1.18
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Model\ResourceModel\Rule;

/**
 * @method \Mirasvit\Rma\Model\Rule getFirstItem()
 * @method \Mirasvit\Rma\Model\Rule getLastItem()
 * @method \Mirasvit\Rma\Model\ResourceModel\Rule\Collection|\Mirasvit\Rma\Model\Rule[] addFieldToFilter
 * @method \Mirasvit\Rma\Model\ResourceModel\Rule\Collection|\Mirasvit\Rma\Model\Rule[] setOrder
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    private $resource;
    /**
     * @var null
     */
    private $connection;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;
    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategyInterface
     */
    private $fetchStrategy;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \Magento\Framework\Data\Collection\EntityFactoryInterface
     */
    private $entityFactory;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->entityFactory = $entityFactory;
        $this->logger        = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager  = $eventManager;
        $this->storeManager  = $storeManager;
        $this->connection    = $connection;
        $this->resource      = $resource;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Rma\Model\Rule', 'Mirasvit\Rma\Model\ResourceModel\Rule');
    }

    /**
     * @param string|false $emptyOption
     *
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = ['value' => 0, 'label' => __('-- Please Select --')];
        }
        /** @var \Mirasvit\Rma\Model\Rule $item */
        foreach ($this as $item) {
            $arr[] = ['value' => $item->getId(), 'label' => $item->getName()];
        }

        return $arr;
    }

    /**
     * @param string|false $emptyOption
     *
     * @return array
     */
    public function getOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = __('-- Please Select --');
        }
        /** @var \Mirasvit\Rma\Model\Rule $item */
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

     /************************/
}
