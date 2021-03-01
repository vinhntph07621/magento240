<?php
/**
 * Project: Omnyfy Core.
 * User: jing
 * Date: 23/10/17
 * Time: 11:14 AM
 */
namespace Omnyfy\Core\Block\Adminhtml;

class Template extends \Magento\Backend\Block\Template
{
    public function getConfigValue($key){
        return $this->_scopeConfig->getValue($key);
    }

    public function isSetFlag($key) {
        return $this->_scopeConfig->isSetFlag($key);
    }
}