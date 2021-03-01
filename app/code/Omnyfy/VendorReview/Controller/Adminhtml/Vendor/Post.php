<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Adminhtml\Vendor;

use Omnyfy\VendorReview\Controller\Adminhtml\Vendor as VendorController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\LocalizedException;

class Post extends VendorController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $vendorId = $this->getRequest()->getParam('vendor_id', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($data = $this->getRequest()->getPostValue()) {
            /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
            $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
            if ($storeManager->hasSingleStore()) {
                $data['stores'] = [
                    $storeManager->getStore(true)->getId(),
                ];
            } elseif (isset($data['select_stores'])) {
                $data['stores'] = $data['select_stores'];
            }
            $review = $this->reviewFactory->create()->setData($data);
            try {
                $review->setEntityId(1) // vendor
                    ->setEntityPkValue($vendorId)
                    ->setStoreId(Store::DEFAULT_STORE_ID)
                    ->setStatusId($data['status_id'])
                    ->setCustomerId(null)//null is for administrator only
                    ->save();

                $arrRatingId = $this->getRequest()->getParam('ratings', []);
                foreach ($arrRatingId as $ratingId => $optionId) {
                    $this->ratingFactory->create()
                        ->setVendorRatingId($ratingId)
                        ->setOmnyfyVendorReviewId($review->getId())
                        ->addOptionVote($optionId, $vendorId);
                }

                $review->aggregate();

                $this->messageManager->addSuccess(__('You saved the review.'));
                if ($this->getRequest()->getParam('ret') == 'pending') {
                    $resultRedirect->setPath('vendorreview/*/pending');
                } else {
                    $resultRedirect->setPath('vendorreview/*/');
                }
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving this review.'));
            }
        }
        $resultRedirect->setPath('vendorreview/*/');
        return $resultRedirect;
    }
}
