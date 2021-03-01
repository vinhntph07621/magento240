<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Block\Article;

use Magento\Store\Model\ScopeInterface;

/**
 * Cms article info block
 */
class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * Block template file
     * @var string
     */
    protected $_template = 'article/info.phtml';

    /**
     * DEPRECATED METHOD!!!!
     * Retrieve formated articleed date
     * @var string
     * @return string
     */
    public function getArticleedOn($format = 'Y-m-d H:i:s')
    {
        return $this->getArticle()->getPublishDate($format);
    }

    /**
     * Retrieve 1 if display author information is enabled
     * @return int
     */
    public function authorEnabled()
    {
        return (int) $this->_scopeConfig->getValue(
            'mfcms/author/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve 1 if author page is enabled
     * @return int
     */
    public function authorPageEnabled()
    {
        return (int) $this->_scopeConfig->getValue(
            'mfcms/author/page_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

}
