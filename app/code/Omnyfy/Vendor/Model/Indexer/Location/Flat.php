<?php

namespace Omnyfy\Vendor\Model\Indexer\Location;

use Magento\Framework\Indexer\CacheContext;

class Flat implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Location\Flat\Action\Row
     */
    protected $_locationFlatIndexerRow;

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Location\Flat\Action\Rows
     */
    protected $_locationFlatIndexerRows;

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Location\Flat\Action\Full
     */
    protected $_locationFlatIndexerFull;

    /**
     * @var \Magento\Framework\Indexer\CacheContext
     */
    private $cacheContext;

    /**
     * @param Flat\Action\Row $locationFlatIndexerRow
     * @param Flat\Action\Rows $locationFlatIndexerRows
     * @param Flat\Action\Full $locationFlatIndexerFull
     */
    public function __construct(
        \Omnyfy\Vendor\Model\Indexer\Location\Flat\Action\Row $locationFlatIndexerRow,
        \Omnyfy\Vendor\Model\Indexer\Location\Flat\Action\Rows $locationFlatIndexerRows,
        \Omnyfy\Vendor\Model\Indexer\Location\Flat\Action\Full $locationFlatIndexerFull
    ) {
        $this->_locationFlatIndexerRow = $locationFlatIndexerRow;
        $this->_locationFlatIndexerRows = $locationFlatIndexerRows;
        $this->_locationFlatIndexerFull = $locationFlatIndexerFull;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->_locationFlatIndexerRows->execute($ids);
        $this->getCacheContext()->registerEntities(\Omnyfy\Vendor\Model\Location::CACHE_TAG, $ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->_locationFlatIndexerFull->execute();
        $this->getCacheContext()->registerTags([\Omnyfy\Vendor\Model\Location::CACHE_TAG]);
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->_locationFlatIndexerRows->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $this->_locationFlatIndexerRow->execute($id);
    }

    /**
     * Get cache context
     *
     * @return \Magento\Framework\Indexer\CacheContext
     * @deprecated
     */
    protected function getCacheContext()
    {
        if (!($this->cacheContext instanceof CacheContext)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(CacheContext::class);
        } else {
            return $this->cacheContext;
        }
    }
}
