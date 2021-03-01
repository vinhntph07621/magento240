<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-08-23
 * Time: 11:23
 */
namespace Omnyfy\Vendor\Plugin\Product;

class Button
{
    protected $resultRedirectFactory;

    protected $messageManager;

    protected $session;

    protected $vendorConfig;

    protected $vendorResource;

    protected $backendHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Backend\Model\Session $session,
        \Omnyfy\Vendor\Model\Config $vendorConfig,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Omnyfy\Vendor\Helper\Backend $backendHelper
    ) {
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->messageManager = $context->getMessageManager();
        $this->session = $session;
        $this->vendorConfig = $vendorConfig;
        $this->vendorResource = $vendorResource;
        $this->backendHelper = $backendHelper;
    }

    public function aroundGetButtonData($subject, callable $process)
    {
        $vendorInfo = $this->session->getVendorInfo();

        if (empty($vendorInfo) || !isset($vendorInfo['vendor_id']) || 0 == $vendorInfo['vendor_id']) {
            return $process();
        }

        //check configuration
        if (!$this->vendorConfig->isIncludeMoProducts() || !$this->vendorConfig->isReadonlyMoProducts()) {
            return $process();
        }

        $moVendorIds = $this->vendorConfig->getMOVendorIds();
        if (in_array($vendorInfo['vendor_id'], $moVendorIds)) {
            return $process();
        }

        $id = $this->backendHelper->getRequest()->getParam('id');
        if (empty($id)) {
            return $process();
        }

        $vendorIds = $this->vendorResource->getVendorIdArrayByProductId($id);
        $found = array_intersect($moVendorIds, $vendorIds);
        if (!empty($found)) {
            return [];
        }

        return $process();
    }

}
 