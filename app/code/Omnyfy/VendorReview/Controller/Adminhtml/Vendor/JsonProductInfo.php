<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Adminhtml\Vendor;

use Omnyfy\VendorReview\Controller\Adminhtml\Vendor as VendorController;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Omnyfy\VendorReview\Model\ReviewFactory;
use Omnyfy\VendorReview\Model\RatingFactory;
use Omnyfy\Vendor\Api\VendorRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Controller\ResultFactory;

class JsonVendorInfo extends VendorController
{
    /**
     * @var \Omnyfy\Vendor\Api\VendorRepositoryInterface
     */
    protected $vendorRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
     * @param \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory
     * @param \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ReviewFactory $reviewFactory,
        RatingFactory $ratingFactory,
        VendorRepositoryInterface $vendorRepository
    ) {
        $this->vendorRepository = $vendorRepository;
        parent::__construct($context, $coreRegistry, $reviewFactory, $ratingFactory);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = new DataObject();
        $id = $this->getRequest()->getParam('id');
        if (intval($id) > 0) {
            $vendor = $this->vendorRepository->getById($id);
            $response->setId($id);
            $response->addData($vendor->getData());
            $response->setError(0);
        } else {
            $response->setError(1);
            $response->setMessage(__('We can\'t retrieve the vendor ID.'));
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($response->toArray());
        return $resultJson;
    }
}
