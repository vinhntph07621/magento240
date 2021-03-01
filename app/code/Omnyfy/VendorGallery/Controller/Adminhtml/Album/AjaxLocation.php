<?php
namespace Omnyfy\VendorGallery\Controller\Adminhtml\Album;

use Magento\Backend\App\Action;
use Omnyfy\Vendor\Model\LocationFactory;
use Magento\Framework\Json\Helper\Data;

class AjaxLocation extends \Magento\Backend\App\Action
{
    /**
     * @var LocationFactory
     */
    protected $locationFactory;

    protected $jsonHelper;

    public function __construct(
        Action\Context $context,
        LocationFactory $locationFactory,
        Data $jsonHelper
    ) {
        $this->locationFactory = $locationFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    public function execute() {
        $vendorId = $this->getRequest()->getParam('vendorId');
        $result = $this->locationFactory->create()->getOptions($vendorId);;
        $this->getResponse()->representJson($this->jsonHelper->jsonEncode($result));
    }
}
