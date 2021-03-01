<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\Vendor\Block\Vendor\View;

class DetailsLink extends \Omnyfy\Core\Block\Element\Html\Link\PageSectionLink {

    /**
     * @var \Omnyfy\Vendor\Block\Vendor\View
     */
    private $_vendorView;

    public function __construct(
        \Omnyfy\Vendor\Block\Vendor\View $vendorView,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_vendorView = $vendorView;
        parent::__construct($context, $data);
    }


    /**
     * Check if there are details to show
     *
     * @return boolean
     */
    public function shouldDisplayDetails() {
        $shouldDisplayDetails = false;
        $excludeFields = ['status', 'entity_id', 'name', 'email', 'attribute_set_id', 'type_id', 'description', 'shipping_policy', 'return_policy', 'payment_policy', 'marketing_policy'];
        $attributes = $this->_vendorView->loadVendorAttributes();

        foreach ($attributes->getData() as $attributeKey => $attributeValue) {
            if (!in_array($attributeKey, $excludeFields)) {
                if ($this->_vendorView->shouldDisplayAttribute($attributeKey)) {
                    $shouldDisplayDetails = true;
                }
            }
        }

        return $shouldDisplayDetails;
    }


    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        if ($this->shouldDisplayDetails()) {
            return '<li><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
        } else {
            return '';
        }
    }
}