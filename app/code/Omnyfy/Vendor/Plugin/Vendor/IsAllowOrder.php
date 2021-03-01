<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 19/9/18
 * Time: 12:02 PM
 */
namespace Omnyfy\Vendor\Plugin\Vendor;

class IsAllowOrder
{
    protected $resultRedirectFactory;

    protected $messageManager;

    protected $session;

    protected $_vendorResource;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Backend\Model\Session $session,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource
    )
    {
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->messageManager = $context->getMessageManager();
        $this->session = $session;
        $this->_vendorResource = $vendorResource;
    }

    public function aroundExecute($subject, callable $process)
    {
        $vendorInfo = $this->session->getVendorInfo();

        if (empty($vendorInfo) || !isset($vendorInfo['vendor_id']) || 0 == $vendorInfo['vendor_id']) {
            return $process();
        }

        $orderId = intval($subject->getRequest()->getParam('order_id'));

        if (!$this->_vendorResource->isOrderForVendor($orderId, intval($vendorInfo['vendor_id']))) {
            $this->messageManager->addErrorMessage(__('You don\'t have permission to access order '. $orderId));
            return $this->resultRedirectFactory->create()
                ->setPath('sales/*');
        }

        return $process();
    }
}