<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 10/9/19
 * Time: 1:02 pm
 */
namespace Omnyfy\VendorSubscription\Block\Adminhtml\History\Renderer;

use Magento\Framework\DataObject;

class Invoice extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(DataObject $row)
    {
        if (empty($row['invoice_link'])) {
            return '';
        }

        return '<a href="' . $row['invoice_link'] . '">' . __('Download') . '</a>';
    }
}
 