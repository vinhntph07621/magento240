<?php

namespace Omnyfy\Vendor\Block\Backend\Grid;

/**
 * Class ItemsUpdater
 * @package Omnyfy\Vendor\Block\Backend\Grid
 */
class ItemsUpdater extends \Magento\Indexer\Block\Backend\Grid\ItemsUpdater
{
    /**
     * @param mixed $argument
     * @return mixed
     */
    public function update($argument)
    {
        if (false === $this->authorization->isAllowed('Magento_Indexer::changeMode')) {
            unset($argument['change_mode_onthefly']);
            unset($argument['change_mode_changelog']);
        }
        if (false === $this->authorization->isAllowed('Omnyfy_Reindex::reindexdata')) {
            unset($argument['change_mode_reindex']);
        }
        return $argument;
    }
}
