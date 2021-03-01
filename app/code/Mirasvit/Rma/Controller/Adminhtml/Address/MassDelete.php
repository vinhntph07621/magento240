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


namespace Mirasvit\Rma\Controller\Adminhtml\Address;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Mirasvit\Rma\Controller\Adminhtml\Address
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
                $collection = $objectManager->create('Mirasvit\Rma\Model\ResourceModel\Address\Collection')->load();
                $ids = $collection->getAllIds();
            } else {
                $this->messageManager->addError(__('Please select Return Address(es)'));
                return $resultRedirect->setPath('*/*/index');
            }
        }
        try {
            foreach ($ids as $id) {
                /** @var \Mirasvit\Rma\Model\Address $address */
                $address = $this->addressFactory->create()
                    ->setIsMassDelete(true)
                    ->load($id);
                $address->delete();
            }
            $this->messageManager->addSuccess(
                sprintf(__('Total of %d record(s) were successfully deleted'), count($ids))
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
