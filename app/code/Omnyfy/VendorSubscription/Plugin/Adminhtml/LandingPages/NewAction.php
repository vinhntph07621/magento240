<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 10/9/19
 * Time: 11:31 am
 */
namespace Omnyfy\VendorSubscription\Plugin\Adminhtml\LandingPages;

use Omnyfy\VendorSubscription\Model\Source\UsageType;

class NewAction
{
    protected $_session;

    protected $_helper;

    protected $resultRedirectFactory;

    protected $messageManager;

    public function __construct(
        \Omnyfy\VendorSubscription\Helper\Usage $_helper,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_helper = $_helper;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
    }

    public function aroundDispatch($subject, callable $process, $request)
    {
        //if it's admin, do nothing
        $vendorInfo = $this->getBackendSession()->getVendorInfo();
        if (empty($vendorInfo)) {
            return $process($request);
        }

        if ($this->_helper->isRunOut($vendorInfo['vendor_id'], UsageType::KIT_STORE)) {
            $this->messageManager->addErrorMessage('You have used up your allocation of kit stores. Please upgrade your plan or contact your marketplace administrator for more information');
            return $this->resultRedirectFactory->create()->setPath('omnyfy_landingpages/page/index');
        }

        return $process($request);
    }

    protected function getBackendSession()
    {
        if (null == $this->_session) {
            $this->_session = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Backend\Model\Session::class);
        }
        return $this->_session;
    }
}
 