<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class BackButton
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $vendorId = $this->context->getRequest()->getParam('vendor_id');
        if (!empty($vendorId)) {
            return $this->getUrl('*/*/', ['vendor_id' => $vendorId]);
        }
        return $this->getUrl('*/*/');
    }
}
