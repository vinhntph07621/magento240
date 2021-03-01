<?php
namespace Omnyfy\Mcm\Block\Adminhtml\Items\Renderer;

use function Amasty\AdminActionsLog\lib\indent_text;

class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Items\Renderer\DefaultRenderer
{
    public function getTemplate()
    {
        //$itemClass = get_class($this->getItem());
        $item = $this->getItem();
        if (!($item instanceof \Magento\Sales\Model\Order\Invoice\Item)
        ) {
            return parent::getTemplate();
        }

        if ($this->_scopeConfig->isSetFlag(\Omnyfy\Mcm\Helper\Data::XML_PATH . \Omnyfy\Mcm\Helper\Data::MCM_ENABLE)) {
            return 'Omnyfy_Mcm::order/invoice/view/items/renderer/default.phtml';
        }
        return 'Omnyfy_Vendor::order/invoice/view/items/renderer/default.phtml';
    }

    public function getOrder()
    {
        if (!empty($this->getItem()->getShipment())) {
            return $this->getItem()->getShipment()->getOrder();
        }

        return parent::getOrder();
    }
}