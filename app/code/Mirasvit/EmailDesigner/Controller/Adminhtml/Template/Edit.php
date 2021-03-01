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
 * @package   mirasvit/module-email-designer
 * @version   1.1.45
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailDesigner\Controller\Adminhtml\Template;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\EmailDesigner\Api\Data\TemplateInterface;
use Mirasvit\EmailDesigner\Controller\Adminhtml\Template;

class Edit extends Template
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id = $this->getRequest()->getParam(TemplateInterface::ID);
        $model = $this->initModel();

        if ($id && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('This item not exists.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        if (!class_exists('Liquid\Liquid')) {
            $this->context->getMessageManager()->addNotice(__(
                "Liquid template engine is not installed." ."</br>".
                "To show Preview page correctly, please install the Liquid package on the server via the SSH command: composer require liquid/liquid':'~1.4"
            ));
        }

        $this->initPage($resultPage)->getConfig()->getTitle()->prepend($id ? $model->getTitle() : __('New Template'));

        return $resultPage;
    }
}
