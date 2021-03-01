<?php

namespace Omnyfy\Vendor\Plugin;

use Omnyfy\Vendor\Helper\Data as  VendorHelper;

class UserSavePlugin
{
    protected $vendorRepository;
    protected $messageManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        VendorHelper $vendorHelper,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendor
    ) {
        $this->vendorHelper = $vendorHelper;
        $this->vendor = $vendor;
        $this->messageManager = $context->getMessageManager();
    }

    public function aroundExecute(\Magento\User\Controller\Adminhtml\User\Save $subject, callable $process)
    {
        $data = $subject->getRequest()->getPostValue();

        if (isset($data['user_id']) && !empty($data['user_id'])) {

            // If vendor is not set remove
            if (isset($data['vendor']) && $data['vendor'] == 'none') {
                $this->vendor->removeUserVendor($data['user_id']);
            }
            elseif (isset($data['vendor']) && !empty($data['vendor'])) {
                $shouldUpdateVendor = $this->vendor->shouldUpdateVendor($data['user_id'], $data['vendor']);
                if ($shouldUpdateVendor == 'vendor_assigned') {
                    $this->messageManager->addErrorMessage('Vendor already assigned to another user! Chosen vendor was not saved!');
                } elseif ($shouldUpdateVendor == 'should_assign') {
                    $this->vendor->updateVendorByUserId($data['user_id'], $data['vendor']);
                }
            }

            // When storing store ID remember to serialize array
            if (isset($data['store_ids']) && !empty($data['store_ids'])) {
                $updateUserStores = $this->vendor->updateUserStores($data['user_id'], $data['store_ids']);
            }

            return $process();
        }

        return $process();
    }
}