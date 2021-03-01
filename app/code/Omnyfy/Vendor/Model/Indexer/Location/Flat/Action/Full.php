<?php

namespace Omnyfy\Vendor\Model\Indexer\Location\Flat\Action;

/**
 * Class Full reindex action
 *
 */
class Full extends \Omnyfy\Vendor\Model\Indexer\Location\Flat\AbstractAction
{
    /**
     * Execute full reindex action
     *
     * @param null|array $ids
     *
     * @return \Omnyfy\Vendor\Model\Indexer\Location\Flat\Action\Full
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($ids = null)
    {
        try {
            foreach ($this->_storeManager->getStores() as $store) {
                $this->_reindex($store->getId());
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
        return $this;
    }
}
