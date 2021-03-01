<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-13
 * Time: 15:38
 */
namespace Omnyfy\VendorSubscription\Plugin\Vendor;

class IsAllow
{
    protected $resultRedirectFactory;

    protected $messageManager;

    protected $session;

    protected $_subResource;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Backend\Model\Session $session,
        \Omnyfy\VendorSubscription\Model\Resource\Subscription $subscriptionResource
    )
    {
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->messageManager = $context->getMessageManager();
        $this->session = $session;
        $this->_subResource = $subscriptionResource;
    }

    public function aroundExecute($subject, callable $process)
    {
        $vendorInfo = $this->session->getVendorInfo();

        if (empty($vendorInfo) || !isset($vendorInfo['vendor_id']) || 0 == $vendorInfo['vendor_id']) {
            return $process();
        }

        $id = intval($subject->getRequest()->getParam('id'));

        if (!$this->_subResource->isSubscriptionForVendor($id, intval($vendorInfo['vendor_id']))) {
            $this->messageManager->addErrorMessage(__('You don\'t have permission to access subscription '. $id));
            return $this->resultRedirectFactory->create()
                ->setPath('omnyfy_subscription/*');
        }

        return $process();
    }
}
 