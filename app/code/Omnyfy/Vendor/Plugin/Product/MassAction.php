<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-08-22
 * Time: 16:24
 */
namespace Omnyfy\Vendor\Plugin\Product;

class MassAction
{
    protected $resultRedirectFactory;

    protected $messageManager;

    protected $session;

    protected $vendorConfig;

    protected $vendorResource;

    protected $request;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Backend\Model\Session $session,
        \Omnyfy\Vendor\Model\Config $vendorConfig,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->messageManager = $context->getMessageManager();
        $this->session = $session;
        $this->vendorConfig = $vendorConfig;
        $this->vendorResource = $vendorResource;
        $this->request = $request;
    }

    public function aroundExecute($subject, callable $process)
    {
        $vendorInfo = $this->session->getVendorInfo();

        $action = $this->request->getActionName();

        if ($action == 'massDelete' && !empty($vendorInfo)) {
            $this->messageManager->addErrorMessage('Sorry, cannot mass delete products.');
            return $this->resultRedirectFactory->create()
                ->setPath('catalog/product/index');
        }

        if (empty($vendorInfo) || !isset($vendorInfo['vendor_id']) || 0 == $vendorInfo['vendor_id']) {
            return $process();
        }

        //check configuration
        if (!$this->vendorConfig->isIncludeMoProducts() || !$this->vendorConfig->isReadonlyMoProducts()) {
            return $process();
        }

        $error = false;
        //if all selected, redirect back with error message
        if ('false' === $subject->getRequest()->getParam('excluded')) {
            $error = true;
        }
        else{
            //load selected ids, check if mo product included
            $ids = $subject->getRequest()->getParam('selected');
            $moVendorIds = $this->vendorConfig->getMOVendorIds();
            $vendorIds = $this->vendorResource->getVendorIdByProducts($ids);
            $vendorIds = array_unique(array_values($vendorIds));

            $found = array_intersect($moVendorIds, $vendorIds);
            if (!empty($found)) {
                $error = true;
            }
        }

        //if error redirect back to product list page with error message
        if ($error) {
            $this->messageManager->addErrorMessage('You cannot update MO\'s products.');
            return $this->resultRedirectFactory->create()
                ->setPath('catalog/product/index');
        }

        return $process();
    }

}
 