<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 19/9/18
 * Time: 2:15 PM
 */
namespace Omnyfy\Vendor\Plugin\Vendor;

class IsAllowInvoice
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

        $invoiceId = intval($subject->getRequest()->getParam('invoice_id'));

        if (!$this->_vendorResource->isInvoiceForVendor($invoiceId, intval($vendorInfo['vendor_id']))) {
            $this->messageManager->addErrorMessage(__('You don\'t have permission to access invoice '. $invoiceId));
            return $this->resultRedirectFactory->create()
                ->setPath('sales/*');
        }

        return $process();
    }
}