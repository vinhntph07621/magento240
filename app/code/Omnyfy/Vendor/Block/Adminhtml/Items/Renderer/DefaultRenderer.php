<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 17/7/17
 * Time: 7:47 PM
 */

namespace Omnyfy\Vendor\Block\Adminhtml\Items\Renderer;

class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Items\Renderer\DefaultRenderer
{
    public function getTemplate()
    {
        $item = $this->getItem();
        if (!($item instanceof \Magento\Sales\Model\Order\Invoice\Item)
        ) {
            return parent::getTemplate();
        }

        return 'Omnyfy_Vendor::order/invoice/view/items/renderer/default.phtml';
    }
}