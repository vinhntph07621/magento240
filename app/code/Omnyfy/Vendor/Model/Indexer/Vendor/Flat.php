<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-26
 * Time: 15:10
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor;

use Magento\Framework\Indexer\CacheContext;

class Flat implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action\Row
     */
    protected $_vendorFlatIndexerRow;

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action\Rows
     */
    protected $_vendorFlatIndexerRows;

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action\Full
     */
    protected $_vendorFlatIndexerFull;

    /**
     * @var \Magento\Framework\Indexer\CacheContext
     */
    private $cacheContext;

    /**
     * @param Flat\Action\Row $vendorFlatIndexerRow
     * @param Flat\Action\Rows $vendorFlatIndexerRows
     * @param Flat\Action\Full $vendorFlatIndexerFull
     */
    public function __construct(
        \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action\Row $vendorFlatIndexerRow,
        \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action\Rows $vendorFlatIndexerRows,
        \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action\Full $vendorFlatIndexerFull
    ) {
        $this->_vendorFlatIndexerRow = $vendorFlatIndexerRow;
        $this->_vendorFlatIndexerRows = $vendorFlatIndexerRows;
        $this->_vendorFlatIndexerFull = $vendorFlatIndexerFull;
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids)
    {
        $this->_vendorFlatIndexerRows->execute($ids);
        $this->getCacheContext()->registerEntities(\Omnyfy\Vendor\Model\Vendor::CACHE_TAG, $ids);
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->_vendorFlatIndexerFull->execute();
        $this->getCacheContext()->registerTags(
            [
                \Omnyfy\Vendor\Model\Vendor::CACHE_TAG,
                \Omnyfy\Vendor\Model\Location::CACHE_TAG
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
        $this->_vendorFlatIndexerRows->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id)
    {
        $this->_vendorFlatIndexerRow->execute($id);
    }

    /**
     * Get cache context
     *
     * @return \Magento\Framework\Indexer\CacheContext
     *
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
 