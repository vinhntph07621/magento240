<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 15/8/17
 * Time: 8:28 AM
 */
namespace Omnyfy\Vendor\Controller\Brands;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;
use Magento\Setup\Exception;

class Store extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    protected $resultForwardFactory;

    protected $vendorRepository;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;

        $this->vendorRepository = $vendorRepository;

        parent::__construct($context);
    }

    public function execute()
    {
        $vendor = $this->_initVendor();

        if (empty($vendor)) {
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
            $vendor = $this->vendorRepository->getById($vendorId);
            if ($vendorId != $vendor->getId()) {
                return false;
            }
            return $vendor;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return false;
        }
    }
}