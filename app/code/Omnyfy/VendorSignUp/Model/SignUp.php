<?php
namespace Omnyfy\VendorSignUp\Model;

use Magento\Framework\Model\AbstractModel;

class SignUp extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSignUp\Model\ResourceModel\SignUp');
    }

    public function getExtraInfoAsArray()
    {
        $info = $this->getData('extra_info');
        if (empty($info)) {
            return [];
        }
        try{
            $info = json_decode($info, true);
            return $info;
        }
        catch (\Exception $e) {
            return [];
        }
    }
}