<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Omnyfy\Cms\Controller\Adminhtml\Import;

/**
 * Cms aw import controller
 */
class Aw extends \Magento\Backend\App\Action
{
    /**
     * Prepare aw import
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_redirect('*/*/');
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Omnyfy_Cms::import');
    }
}
