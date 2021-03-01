<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-08
 * Time: 17:13
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Eav\Action;

class Full extends \Omnyfy\Vendor\Model\Indexer\Vendor\Eav\AbstractAction
{
    /**
     * Execute Full reindex
     *
     * @param array|int|null $ids
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($ids = null)
    {
        try {
            $this->reindex();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }
}
 