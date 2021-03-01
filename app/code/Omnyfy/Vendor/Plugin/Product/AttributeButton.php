<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-08-23
 * Time: 11:30
 */
namespace Omnyfy\Vendor\Plugin\Product;

class AttributeButton
{
    protected $resultRedirectFactory;

    protected $messageManager;

    protected $session;

    protected $vendorConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Backend\Model\Session $session,
        \Omnyfy\Vendor\Model\Config $vendorConfig,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource
    ) {
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->messageManager = $context->getMessageManager();
        $this->session = $session;
        $this->vendorConfig = $vendorConfig;
    }

    public function aroundGetButtonData($subject, callable $process)
    {
        $vendorInfo = $this->session->getVendorInfo();

        if (empty($vendorInfo) || !isset($vendorInfo['vendor_id']) || 0 == $vendorInfo['vendor_id']) {
            return $process();
        }

        $moVendorIds = $this->vendorConfig->getMOVendorIds();
        if (in_array($vendorInfo['vendor_id'], $moVendorIds)) {
            return $process();
        }

        //Hide add attribute button for all vendors
        return [];
    }

}
 