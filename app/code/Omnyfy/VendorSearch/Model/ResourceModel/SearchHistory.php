<?php


namespace Omnyfy\VendorSearch\Model\ResourceModel;

class SearchHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('omnyfy_vendorsearch_searchhistory', 'searchhistory_id');
    }
}
