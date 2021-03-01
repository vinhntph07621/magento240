<?php

namespace Omnyfy\Mcm\Model\ResourceModel\MarketplaceCommissionReport;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * Define resource model.
     */
    public function _construct()
    {
        $this->_init('Omnyfy\Mcm\Model\MarketplaceCommissionReport',
                'Omnyfy\Mcm\Model\ResourceModel\MarketplaceCommissionReport');
    }
}