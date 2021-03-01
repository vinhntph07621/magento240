<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Rss;

use Magento\Store\Model\ScopeInterface;

/**
 * Cms ree feed block
 */
class Feed extends \Omnyfy\Cms\Block\Article\ArticleList\AbstractList
{
    /**
     * Retrieve rss feed url
     * @return string
     */
    public function getLink()
    {
        return $this->_url->getUrl('feed', 'rss');
    }

    /**
     * Retrieve rss feed title
     * @return string
     */
    public function getTitle()
    {
    	 return $this->_scopeConfig->getValue('mfcms/rss_feed/title', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve rss feed description
     * @return string
     */
    public function getDescription()
    {
    	 return $this->_scopeConfig->getValue('mfcms/rss_feed/description', ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve block identities
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_cms_rss_feed'  ];
    }

}
