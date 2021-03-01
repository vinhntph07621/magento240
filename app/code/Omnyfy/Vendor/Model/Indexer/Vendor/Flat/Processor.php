<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-18
 * Time: 14:59
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Flat;

class Processor extends \Magento\Framework\Indexer\AbstractProcessor
{
    const INDEXER_ID = 'omnyfy_vendor_vendor_flat';

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\State
     */
    protected $_state;

    /**
     * Processor constructor.
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     * @param \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\State $state
     */
    public function __construct(
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\State $state
    ) {
        parent::__construct($indexerRegistry);
        $this->_state = $state;
    }

    /**
     * Reindex single row by id
     *
     * @param int $id
     * @param bool $forceReindex
     * @return void
     */
    public function reindexRow($id, $forceReindex = false)
    {
        if (!$this->_state->isFlatEnabled() || (!$forceReindex && $this->getIndexer()->isScheduled())) {
            return;
        }
        $this->getIndexer()->reindexRow($id);
    }

    /**
     * Reindex multiple rows by ids
     *
     * @param int[] $ids
     * @param bool $forceReindex
     * @return void
     */
    public function reindexList($ids, $forceReindex = false)
    {
        if (!$this->_state->isFlatEnabled() || (!$forceReindex && $this->getIndexer()->isScheduled())) {
            return;
        }
        $this->getIndexer()->reindexList($ids);
    }

    /**
     * Run full reindex
     *
     * @return void
     */
    public function reindexAll()
    {
        if (!$this->_state->isFlatEnabled()) {
            return;
        }
        $this->getIndexer()->reindexAll();
    }

    /**
     * Mark Product flat indexer as invalid
     *
     * @return void
     */
    public function markIndexerAsInvalid()
    {
        if (!$this->_state->isFlatEnabled()) {
            return;
        }
        $this->getIndexer()->invalidate();
    }
}
 