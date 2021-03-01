<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-08
 * Time: 16:38
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Eav;

/**
 * Abstract action reindex class
 */
abstract class AbstractAction
{
    /**
     * EAV Indexers by type
     *
     * @var array
     */
    protected $_types;

    /**
     * @var \Omnyfy\Vendor\Model\Resource\Vendor\Indexer\Eav\SourceFactory
     */
    protected $_eavSourceFactory;

    /**
     * @var \Omnyfy\Vendor\Model\Resource\Vendor\Indexer\Eav\DecimalFactory
     */
    protected $_eavDecimalFactory;

    /**
     * @param \Omnyfy\Vendor\Model\Resource\Vendor\Indexer\Eav\DecimalFactory $eavDecimalFactory
     * @param \Omnyfy\Vendor\Model\Resource\Vendor\Indexer\Eav\SourceFactory $eavSourceFactory
     */
    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Vendor\Indexer\Eav\DecimalFactory $eavDecimalFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor\Indexer\Eav\SourceFactory $eavSourceFactory
    ) {
        $this->_eavDecimalFactory = $eavDecimalFactory;
        $this->_eavSourceFactory = $eavSourceFactory;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     * @return void
     */
    abstract public function execute($ids);

    /**
     * Retrieve array of EAV type indexers
     *
     * @return \Omnyfy\Vendor\Model\Resource\Vendor\Indexer\Eav\AbstractEav[]
     */
    public function getIndexers()
    {
        if ($this->_types === null) {
            $this->_types = [
                'source' => $this->_eavSourceFactory->create(),
                'decimal' => $this->_eavDecimalFactory->create(),
            ];
        }

        return $this->_types;
    }

    /**
     * Retrieve indexer instance by type
     *
     * @param string $type
     * @return \Omnyfy\Vendor\Model\Resource\Vendor\Indexer\Eav\AbstractEav
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIndexer($type)
    {
        $indexers = $this->getIndexers();
        if (!isset($indexers[$type])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Unknown EAV indexer type "%1".', $type));
        }
        return $indexers[$type];
    }

    /**
     * Reindex entities
     *
     * @param null|array|int $ids
     * @return void
     */
    public function reindex($ids = null)
    {
        foreach ($this->getIndexers() as $indexer) {
            if ($ids === null) {
                $indexer->reindexAll();
            } else {
                $indexer->reindexEntities($ids);
            }
        }
    }
}
 