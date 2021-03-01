<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 3/09/2019
 * Time: 6:31 PM
 */

namespace Omnyfy\VendorDashBoard\Model\ResourceModel\DashBoard;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Mirasvit\Dashboard\Model\Board',
            'Mirasvit\Dashboard\Model\ResourceModel\Board'
        );
    }

    public function _initSelect(){
        parent::_initSelect();

        $this->getSelect()->where(
            'type = "private"'
        );

        return $this;
    }
}