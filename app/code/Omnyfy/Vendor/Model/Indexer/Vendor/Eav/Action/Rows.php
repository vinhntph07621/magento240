<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-08
 * Time: 17:12
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Eav\Action;

class Rows extends \Omnyfy\Vendor\Model\Indexer\Vendor\Eav\AbstractAction
{
    /**
     * Execute Rows reindex
     *
     * @param array $ids
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($ids)
    {
        if (empty($ids)) {
            throw new \Magento\Framework\Exception\InputException(__('Bad value was supplied.'));
        }
        try {
            $this->reindex($ids);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }
}
 