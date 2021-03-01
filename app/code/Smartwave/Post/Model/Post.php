<?php

namespace Smartwave\Post\Model;

class Post extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Smartwave\Post\Model\ResourceModel\Post');
    }
}
