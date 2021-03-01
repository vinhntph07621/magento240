<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 7/8/17
 * Time: 11:57 AM
 */

namespace Omnyfy\Vendor\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;

class Location extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $resultForwardFactory;

    protected $locationRepository;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Omnyfy\Vendor\Api\LocationRepositoryInterface $locationRepository
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;

        $this->locationRepository = $locationRepository;

        parent::__construct($context);
    }

    public function execute()
    {
        $vendor = $this->_initVendor();
        if (empty($vendor)) {
            //404
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
        return $this->resultPageFactory->create();
    }

    protected function _initVendor() {
        $vendorId = $this->getRequest()->getParam('id');

        if (empty($vendorId)) return false;

        try {
            $vendor = $this->locationRepository->getById($vendorId);
            if ($vendorId != $vendor->getId()) {
                return false;
            }
            /*
            if (!$vendor->getStatus()) {
                return false;
            }
            */
            return $vendor;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }
    }
}