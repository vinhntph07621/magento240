<?php

namespace Omnyfy\Mcm\Model\ResourceModel;

class MarketplaceCommissionReport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb 
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Construct.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null                                       $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
    }

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('sales_order', 'id');
    }
}