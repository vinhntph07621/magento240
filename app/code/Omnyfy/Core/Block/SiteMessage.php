<?php
/**
 * Copyright © Omnyfy, Inc. All rights reserved.
 *
 * Author: Kateryna Bieliaieva
 * Date: 15/11/2020
 */

namespace Omnyfy\Core\Block;

class SiteMessage extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_SITE_MESSAGE_ENABLED = "omnyfy_core/site_message/site_message_enabled";
    const XML_PATH_SITE_MESSAGE_TEXT = "omnyfy_core/site_message/site_message_text";
    const XML_PATH_SITE_MESSAGE_IE_ONLY = "omnyfy_core/site_message/site_message_ie_only";

    public function isSiteMessageEnabled() {
        return $this->_scopeConfig->getValue($this::XML_PATH_SITE_MESSAGE_ENABLED);
    }

    public function getSiteMessage() {
        return $this->_scopeConfig->getValue($this::XML_PATH_SITE_MESSAGE_TEXT);
    }

    public function isIeOnly() {
        return $this->_scopeConfig->getValue($this::XML_PATH_SITE_MESSAGE_IE_ONLY);
    }
}
