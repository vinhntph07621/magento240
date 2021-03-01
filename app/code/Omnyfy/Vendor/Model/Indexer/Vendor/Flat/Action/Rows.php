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

class Rows extends \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\AbstractAction
{
    /**
     * @var Eraser
     */
    protected $flatItemEraser;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer $indexerHelper
     * @param TableBuilder $tableBuilder
     * @param FlatTableBuilder $flatTableBuilder
     * @param Eraser $flatItemEraser
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer $indexerHelper,
        TableBuilder $tableBuilder,
        FlatTableBuilder $flatTableBuilder,
        Eraser $flatItemEraser
    ) {
        parent::__construct(
            $resource,
            $storeManager,
            $indexerHelper,
            $tableBuilder,
            $flatTableBuilder
        );
        $this->flatItemEraser = $flatItemEraser;
    }

    /**
     * Execute multiple rows reindex action
     *
     * @param array $ids
     *
     * @return \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action\Rows
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($ids)
    {
        if (empty($ids)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Bad value was supplied.'));
        }
        foreach ($this->_storeManager->getStores() as $store) {
            $tableExists = $this->_isFlatTableExists($store->getId());
            $idsBatches = array_chunk($ids, \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer::BATCH_SIZE);
            foreach ($idsBatches as $changedIds) {
                if ($tableExists) {
                    $this->flatItemEraser->removeDeletedProducts($changedIds, $store->getId());
                }
                if (!empty($changedIds)) {
                    $this->_reindex($store->getId(), $changedIds);
                }
            }
        }
        return $this;
    }
}
 