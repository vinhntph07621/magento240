<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 3/11/18
 * Time: 10:36 AM
 */
namespace Omnyfy\Vendor\Plugin\Vendor;

class IsAllowEditRule
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

        $ruleId = intval($subject->getRequest()->getParam('id'));

        if (empty($ruleId)){
            return $process();
        }

        if (!$this->_vendorResource->isRuleForVendor($ruleId, intval($vendorInfo['vendor_id']))) {
            $this->messageManager->addErrorMessage(__('You don\'t have permission to access rule '. $ruleId));
            return $this->resultRedirectFactory->create()
                ->setPath('sales_rule/*');
        }

        return $process();
    }
}