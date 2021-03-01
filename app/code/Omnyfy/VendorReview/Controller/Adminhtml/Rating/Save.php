<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Adminhtml\Rating;

use Omnyfy\VendorReview\Controller\Adminhtml\Rating as RatingController;
use Magento\Framework\Controller\ResultFactory;

class Save extends RatingController
{
    /**
     * Save rating
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $this->initEnityId();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var \Omnyfy\VendorReview\Model\Rating $ratingModel */
                $ratingModel = $this->_objectManager->create('Omnyfy\VendorReview\Model\Rating');
                $stores = $this->getRequest()->getParam('stores');
                $position = (int)$this->getRequest()->getParam('position');
                $stores[] = 0;
                $isActive = (bool)$this->getRequest()->getParam('is_active');

                $ratingModel->setVendorRatingCode($this->getRequest()->getParam('vendor_rating_code'))
                    ->setVendorRatingCodes($this->getRequest()->getParam('vendor_rating_codes'))
                    ->setStores($stores)
                    ->setPosition($position)
                    ->setId($this->getRequest()->getParam('id'))
                    ->setIsActive($isActive)
                    ->setEntityId($this->coreRegistry->registry('entityId'))
                    ->save();

                $options = $this->getRequest()->getParam('option_title');

                if (is_array($options)) {
                    $i = 1;
                    foreach ($options as $key => $optionCode) {
                        $optionModel = $this->_objectManager->create('Omnyfy\VendorReview\Model\Rating\Option');
                        if (!preg_match("/^add_([0-9]*?)$/", $key)) {
                            $optionModel->setId($key);
                        }

                        $optionModel->setCode($optionCode)
                            ->setValue($i)
                            ->setVendorRatingId($ratingModel->getId())
                            ->setPosition($i)
                            ->save();
                        $i++;
                    }
                }

                $this->messageManager->addSuccess(__('You saved the rating.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setRatingData(false);
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_objectManager->get('Magento\Backend\Model\Session')
                    ->setRatingData($this->getRequest()->getPostValue());
                $resultRedirect->setPath('vendorreview/rating/edit', ['id' => $this->getRequest()->getParam('id')]);
                return $resultRedirect;
            }
        }
        $resultRedirect->setPath('vendorreview/rating/');
        return $resultRedirect;
    }
}
