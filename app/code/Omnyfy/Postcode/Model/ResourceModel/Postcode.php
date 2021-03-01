<?php

namespace Omnyfy\Postcode\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Postcode extends AbstractDb
{

    const TABLE_NAME = 'omnyfy_postcode';

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'postcode_id');
    }

}
