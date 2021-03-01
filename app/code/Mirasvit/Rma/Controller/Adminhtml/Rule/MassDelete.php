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



namespace Mirasvit\Rma\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Mirasvit\Rma\Controller\Adminhtml\Rule
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $ids = $this->getRequest()->getParam('selected');
        if (!is_array($ids)) {
            if ($this->getRequest()->getParam('excluded')) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $collection = $objectManager->create('Mirasvit\Rma\Model\ResourceModel\Rule\Collection')->load();
                $ids = $collection->getAllIds();
            } else {
                $this->messageManager->addErrorMessage(__('Please select Rule(s)'));
                return $resultRedirect->setPath('*/*/index');
            }
        }
        try {
            foreach ($ids as $id) {
                /** @var \Mirasvit\Rma\Model\Rule $rule */
                $rule = $this->ruleFactory->create()
                    ->setIsMassDelete(true)
                    ->load($id);
                $rule->delete();
            }
            $this->messageManager->addSuccessMessage(
                sprintf(__('Total of %d record(s) were successfully deleted'), count($ids))
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
