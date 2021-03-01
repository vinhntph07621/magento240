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
 * @package   mirasvit/module-email
 * @version   2.1.44
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Controller\Adminhtml\Trigger;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Email\Api\Data\TriggerInterface;
use Mirasvit\Email\Controller\Adminhtml\Trigger;

class Edit extends Trigger
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id = $this->getRequest()->getParam(TriggerInterface::ID);
        $model = $this->initModel();

        // Register flags to display rules
        $this->registry->register('event_formName', 'email_trigger_form');
        if ($model->getId()) {
            $this->registry->register('event_eventIdentifier', $model->getEvent());
            $this->registry->register('event_ruleConditions', $model->getRule());
        }

        if ($id && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('This item no longer exists.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $this->initPage($resultPage)->getConfig()->getTitle()->prepend($id ? $model->getTitle() : __('New Trigger'));

        return $resultPage;
    }
}
