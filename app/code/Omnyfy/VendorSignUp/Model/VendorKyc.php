<?php

namespace Omnyfy\VendorSignUp\Model;

use Magento\Framework\Model\AbstractModel;

class VendorKyc extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSignUp\Model\ResourceModel\VendorKyc');
    }
}
