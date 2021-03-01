<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller\Author;

use \Magento\Store\Model\ScopeInterface;

/**
 * Cms author articles view
 */
class View extends \Magento\Framework\App\Action\Action
{
    /**
     * View cms author action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $config = $this->_objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');

        $enabled = (int) $config->getValue('mfcms/author/enabled',
            ScopeInterface::SCOPE_STORE);
        $pageEnabled = (int) $config->getValue('mfcms/author/page_enabled',
            ScopeInterface::SCOPE_STORE);

        if (!$enabled || !$pageEnabled) {
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        $author = $this->_initAuthor();
        if (!$author) {
            $this->_forward('index', 'noroute', 'cms');
            return;
        }

        $this->_objectManager->get('\Magento\Framework\Registry')->register('current_cms_author', $author);

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

    /**
     * Init author
     *
     * @return \Omnyfy\Cms\Model\Author || false
     */
    protected function _initAuthor()
    {
        $id = $this->getRequest()->getParam('id');

        $author = $this->_objectManager->create('Omnyfy\Cms\Model\Author')->load($id);

        if (!$author->getId()) {
            return false;
        }

        return $author;
    }

}
