<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-26
 * Time: 15:16
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action;

use Omnyfy\Vendor\Model\Indexer\Vendor\Flat\FlatTableBuilder;
use Omnyfy\Vendor\Model\Indexer\Vendor\Flat\TableBuilder;

class Row extends \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\AbstractAction
{
    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action\Indexer
     */
    protected $flatItemWriter;

    /**
     * @var Eraser
     */
    protected $flatItemEraser;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omnyfy\Vendor\Helper\Location\Flat\Indexer $indexHelper
     * @param TableBuilder $tableBuilder
     * @param FlatTableBuilder $flatTableBuilder
     * @param Indexer $flatItemWriter
     * @param Eraser $flatItemEraser
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer $indexerHelper,
        TableBuilder $tableBuilder,
        FlatTableBuilder $flatTableBuilder,
        Indexer $flatItemWriter,
        Eraser $flatItemEraser
    ) {
        parent::__construct(
            $resource,
            $storeManager,
            $indexerHelper,
            $tableBuilder,
            $flatTableBuilder
        );
        $this->flatItemWriter = $flatItemWriter;
        $this->flatItemEraser = $flatItemEraser;
    }

    /**
     * Execute row reindex action
     *
     * @param int|null $id
     * @return \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action\Row
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($id = null)
    {
        if (!isset($id) || empty($id)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t rebuild the index for an undefined vendor.')
            );
        }
        $ids = [$id];
        foreach ($this->_storeManager->getStores() as $store) {
            $tableExists = $this->_isFlatTableExists($store->getId());
            if ($tableExists) {
                $this->flatItemEraser->removeDeletedLocations($ids, $store->getId());
            }
            if (isset($ids[0])) {
                if (!$tableExists) {
                    $this->_flatTableBuilder->build(
                        $store->getId(),
                        [$ids[0]],
                        $this->_valueFieldSuffix,
                        $this->_tableDropSuffix,
                        false
                    );
                }
                $this->flatItemWriter->write($store->getId(), $ids[0], $this->_valueFieldSuffix);
            }
        }
        return $this;
    }
}
 