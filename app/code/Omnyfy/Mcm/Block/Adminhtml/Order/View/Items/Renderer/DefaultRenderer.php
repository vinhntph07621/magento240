<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Order\View\Items\Renderer;

class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer
{
    public function getTemplate()
    {
        if ($this->_scopeConfig->isSetFlag(\Omnyfy\Mcm\Helper\Data::XML_PATH . \Omnyfy\Mcm\Helper\Data::MCM_ENABLE)) {
            return 'Omnyfy_Mcm::order/view/items/renderer/default.phtml';
        }
        return 'Omnyfy_Vendor::order/view/items/renderer/default.phtml';
    }
}