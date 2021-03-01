<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   2.1.18
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Controller\Adminhtml\Reason;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Mirasvit\Rma\Controller\Adminhtml\Reason
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->_initReason();
        $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend(__('New Reason'));
        $this->_addBreadcrumb(
            __('Reason  Manager'),
            __('Reason Manager'),
            $this->getUrl('*/*/')
        );
        $this->_addBreadcrumb(__('Add Reason '), __('Add Reason'));

        return $resultPage;
    }
}
