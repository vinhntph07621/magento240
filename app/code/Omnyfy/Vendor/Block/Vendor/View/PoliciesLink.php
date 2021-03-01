<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\Vendor\Block\Vendor\View;

class PoliciesLink extends \Omnyfy\Core\Block\Element\Html\Link\PageSectionLink {

    /**
     * @var \Omnyfy\Vendor\Block\Vendor\View\Policy
     */
    private $_vendorView;

    public function __construct(
        \Omnyfy\Vendor\Block\Vendor\View\Policy $vendorPolicy,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_vendorPolicy = $vendorPolicy;
        parent::__construct($context, $data);
    }


    /**
     * Check if there are details to show
     *
     * @return boolean
     */
    public function shouldDisplayPolicies() {
        $showPolicies = false;
        $policyContents = $this->_vendorPolicy->getPolicyContents();

        foreach($policyContents as $policy) {
            if ($policy['content'] != null) {
                $showPolicies = true;
            }
        }

        return $showPolicies;
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

        if ($this->shouldDisplayPolicies()) {
            return '<li><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
        } else {
            return '';
        }
    }
}