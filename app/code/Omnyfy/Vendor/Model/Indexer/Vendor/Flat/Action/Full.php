<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-26
 * Time: 15:16
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action;

class Full extends \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\AbstractAction
{
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
 