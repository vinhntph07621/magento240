<?php

namespace Omnyfy\Vendor\Model\Indexer\Location\Flat\System\Config;

/**
 * Flat product on/off backend
 */
class Mode extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Location\Flat\Processor
     */
    protected $_locationFlatIndexerProcessor;

    /**
     * @var \Magento\Indexer\Model\Indexer\State
     */
    protected $indexerState;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Omnyfy\Vendor\Model\Indexer\Location\Flat\Processor $locationFlatIndexerProcessor
     * @param \Magento\Indexer\Model\Indexer\State $indexerState
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Omnyfy\Vendor\Model\Indexer\Location\Flat\Processor $locationFlatIndexerProcessor,
        \Magento\Indexer\Model\Indexer\State $indexerState,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_locationFlatIndexerProcessor = $locationFlatIndexerProcessor;
        $this->indexerState = $indexerState;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Set after commit callback
     *
     * @return $this
     */
    public function afterSave()
    {
        $this->_getResource()->addCommitCallback([$this, 'processValue']);
        return parent::afterSave();
    }

    /**
     * Process flat enabled mode change
     *
     * @return void
     */
    public function processValue()
    {
        if ((bool)$this->getValue() != (bool)$this->getOldValue()) {
            if ((bool)$this->getValue()) {
                $this->indexerState->loadByIndexer(\Omnyfy\Vendor\Model\Indexer\Location\Flat\Processor::INDEXER_ID);
                $this->indexerState->setStatus(\Magento\Framework\Indexer\StateInterface::STATUS_INVALID);
                $this->indexerState->save();
            } else {
                $this->_locationFlatIndexerProcessor->getIndexer()->setScheduled(false);
            }
        }
    }
}
