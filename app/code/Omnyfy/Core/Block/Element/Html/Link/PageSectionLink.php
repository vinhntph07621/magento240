<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\Core\Block\Element\Html\Link;

class PageSectionLink extends \Magento\Framework\View\Element\Html\Link {

    /**
     * Get href URL
     *
     * @return string
     */
    public function getHref()
    {
        return trim($this->getUrl(), '/') . $this->_request->getPathInfo() . $this->getPath();
    }
}