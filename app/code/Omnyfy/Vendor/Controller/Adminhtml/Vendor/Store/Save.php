<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-24
 * Time: 11:29
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Store;

use Magento\Framework\Stdlib\DateTime\Filter\DateTime;

class Save extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendor_stores';

    protected $vendorFactory;

    private $dateTimeFilter;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        DateTime $dateTimeFilter
    ) {
        $this->vendorFactory = $vendorFactory;
        $this->dateTimeFilter = $dateTimeFilter;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute()
    {
        $vendorId = $this->getRequest()->getParam('id');

        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data && !empty($vendorId)) {
            try{
                $vendor = $this->loadVendor($vendorId);

                if (!$vendor->getId() || $vendorId !== $vendor->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Unable to save vendor store data'));
                }

                $this->initFromData($vendor, $data['vendor']);
                $vendor->save();

                $this->messageManager->addSuccessMessage('You saved the vendor store data.');
            }
            catch(\Exception $e) {
                $this->_logger->critical($e);
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setPath(
                    'omnyfy_vendor/*/edit',
                    [ 'id' => $vendorId, '_current' => true]
                );
            }
        }
        else {
            $resultRedirect->setPath('omnyfy_vendor/*/');
            $this->messageManager->addErrorMessage('No data to save');
            return $resultRedirect;
        }

        $resultRedirect->setPath('omnyfy_vendor/*/');
        return $resultRedirect;
    }

    protected function loadVendor($vendorId)
    {
        $vendor = $this->vendorFactory->create();
        if ($vendorId) {
            try {
                $vendor->load($vendorId);
            }
            catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }

        $this->_coreRegistry->register('current_omnyfy_vendor_store', $vendor);
        return $vendor;
    }

    protected function initFromData($vendor, $data)
    {
        unset($data['custom_attributes']);
        unset($data['extension_attributes']);

        $dateFieldFilters = [];
        $attributes = $vendor->getResource()->loadAllAttributes($vendor)
            ->getSortedAttributes($vendor->getAttributeSetId());
        foreach($attributes as $key => $attribute) {
            if ($attribute->getBackend()->getType() == 'datetime') {
                if (array_key_exists($key, $data) && $data[$key] != '') {
                    $dateFieldFilters[$key] = $this->dateTimeFilter;
                }
            }
            if ('image' == $attribute->getFrontendInput() || 'media_image' == $attribute->getFrontendInput() ) {
                if (array_key_exists($key, $data) && is_array($data[$key])) {
                    if (!empty($data[$key]['delete'])) {
                        $data[$key] = null;
                    } else {
                        if (isset($data[$key][0]['name']) && isset($data[$key][0]['tmp_name'])) {
                            $data[$key] = $data[$key][0]['name'];
                        } else {
                            unset($data[$key]);
                        }
                    }
                }
            }
        }

        $inputFilter = new \Zend_Filter_Input($dateFieldFilters, [], $data);
        $data = $inputFilter->getUnescaped();

        if (isset($data['options'])) {
            $vendorOptions = $data['options'];
            unset($data['options']);
        } else {
            $vendorOptions = [];
        }

        $vendor->addData($data);

        return $vendor;
    }
}
 