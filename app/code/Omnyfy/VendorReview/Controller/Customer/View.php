<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Customer;

use Omnyfy\VendorReview\Controller\Customer as CustomerController;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Omnyfy\VendorReview\Model\ReviewFactory;
use Magento\Framework\Controller\ResultFactory;

class View extends CustomerController
{
    /**
     * @var \Omnyfy\VendorReview\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        ReviewFactory $reviewFactory
    ) {
        $this->reviewFactory = $reviewFactory;
        parent::__construct($context, $customerSession);
    }
    /**
     * Render review details
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $review = $this->reviewFactory->create()->load($this->getRequest()->getParam('id'));
        if ($review->getCustomerId() != $this->customerSession->getCustomerId()) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('vendorreview/customer');
        }
        $resultPage->getConfig()->getTitle()->set(__('Review Details'));
        return $resultPage;
    }
}
