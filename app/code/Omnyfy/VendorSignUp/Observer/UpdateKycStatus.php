<?php
/**
 * Project: Vendor Sign Up
 * User: jing
 * Date: 23/1/20
 * Time: 3:57 pm
 */
namespace Omnyfy\VendorSignUp\Observer;

class UpdateKycStatus implements \Magento\Framework\Event\ObserverInterface
{
    protected $vendorKycResource;

    protected $_logger;

    public function __construct(
        \Omnyfy\VendorSignUp\Model\ResourceModel\VendorKyc $vendorKycResource,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->vendorKycResource = $vendorKycResource;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendorId = $observer->getData('vendor_id');
        $status = $observer->getData('status');

        $this->_logger->debug('HERE: ', $observer->getData());

        //update kyc status by vendor_id
        $this->vendorKycResource->updateStatusByVendorId($vendorId, $status);
    }
}
 