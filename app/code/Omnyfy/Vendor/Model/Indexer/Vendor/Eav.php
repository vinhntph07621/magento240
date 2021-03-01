<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-08
 * Time: 16:31
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor;

use Magento\Framework\Indexer\CacheContext;

class Eav implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Eav\Action\Row
     */
    protected $_vendorEavIndexerRow;

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Eav\Action\Rows
     */
    protected $_vendorEavIndexerRows;

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Eav\Action\Full
     */
    protected $_vendorEavIndexerFull;

    /**
     * @var \Magento\Framework\Indexer\CacheContext
     */
    private $cacheContext;

    /**
     * @param Eav\Action\Row $vendorEavIndexerRow
     * @param Eav\Action\Rows $vendorEavIndexerRows
     * @param Eav\Action\Full $vendorEavIndexerFull
     */
    public function __construct(
        \Magento\Catalog\Model\Indexer\Product\Eav\Action\Row $vendorEavIndexerRow,
        \Magento\Catalog\Model\Indexer\Product\Eav\Action\Rows $vendorEavIndexerRows,
        \Magento\Catalog\Model\Indexer\Product\Eav\Action\Full $vendorEavIndexerFull
    ) {
        $this->_vendorEavIndexerRow = $vendorEavIndexerRow;
        $this->_vendorEavIndexerRows = $vendorEavIndexerRows;
        $this->_vendorEavIndexerFull = $vendorEavIndexerFull;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->_vendorEavIndexerRows->execute($ids);
        $this->getCacheContext()->registerEntities(\Omnyfy\Vendor\Model\Vendor::CACHE_TAG, $ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->_vendorEavIndexerFull->execute();
        $this->getCacheContext()->registerTags(
            [
                \Omnyfy\Vendor\Model\Vendor::CACHE_TAG
            ]
        );
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids)
    {
        $this->_vendorEavIndexerRows->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $this->_vendorEavIndexerRow->execute($id);
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
 