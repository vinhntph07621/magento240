<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-08
 * Time: 16:36
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Eav\Action;

/**
 * Class Row reindex action
 */
class Row extends \Omnyfy\Vendor\Model\Indexer\Vendor\Eav\AbstractAction
{
    /**
     * Execute Row reindex
     *
     * @param int|null $id
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($id = null)
    {
        if (!isset($id) || empty($id)) {
            throw new \Magento\Framework\Exception\InputException(
                __('We can\'t rebuild the index for an undefined product.')
            );
        }
        try {
            $this->reindex($id);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }
    }
}

 