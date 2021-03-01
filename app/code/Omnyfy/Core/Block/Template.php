<?php
/**
 * Project: Omnyfy core.
 * User: jing
 * Date: 25/10/17
 * Time: 11:21 AM
 */
namespace Omnyfy\Core\Block;

class Template extends \Magento\Framework\View\Element\Template
{
    public function getConfigValue($key) {
        return $this->_scopeConfig->getValue($key);
    }

    public function isSetFlag($key) {
        return $this->_scopeConfig->isSetFlag($key);
    }
}