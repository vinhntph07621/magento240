<?php

namespace Omnyfy\Core\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\State $appState
    ) {
        $this->_appState = $appState;

        parent::__construct($context);
    }

    /**
     * Is flat enabled?
     *
     * @return boolean
     */
    public function isEnabledFlat()
    {
        return \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE != $this->_appState->getAreaCode();
    }

    public function genUuid($str)
    {
        $string = md5($str);

        $string = substr($string, 0, 8) . '-' .
            substr($string, 8, 4) . '-' .
            substr($string, 12, 4) . '-' .
            substr($string, 16, 4) . '-' .
            substr($string, 20);

        return $string;
    }
}
