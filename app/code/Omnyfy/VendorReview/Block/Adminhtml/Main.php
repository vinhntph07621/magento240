<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml review main block
 */
namespace Omnyfy\VendorReview\Block\Adminhtml;

class Main extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Catalog vendor model factory
     *
     * @var \Magento\Catalog\Model\VendorFactory
     */
    protected $_vendorFactory;

    /**
     * Customer View Helper
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Catalog\Model\VendorFactory $vendorFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Helper\View $customerViewHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->customerRepository = $customerRepository;
        $this->_vendorFactory = $vendorFactory;
        $this->_customerViewHelper = $customerViewHelper;
        parent::__construct($context, $data);
    }

    /**
     * Initialize add new review
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_addButtonLabel = __('New Review');
        parent::_construct();

        $this->_blockGroup = 'Omnyfy_VendorReview';
        $this->_controller = 'adminhtml';

        // lookup customer, if id is specified
        $customerId = $this->getRequest()->getParam('customerId', false);
        $customerName = '';
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $customerName = $this->escapeHtml($this->_customerViewHelper->getCustomerName($customer));
        }
        $vendorId = $this->getRequest()->getParam('vendorId', false);
        $vendorName = null;
        if ($vendorId) {
            $vendor = $this->_vendorFactory->create()->load($vendorId);
            $vendorName = $this->escapeHtml($vendor->getName());
        }


        if ($this->_coreRegistry->registry('usePendingFilter') === true) {
            if ($customerName) {
                $this->_headerText = __('Pending Reviews of Customer `%1`', $customerName);
            } else {
                $this->_headerText = __('Pending Reviews');
            }
            $this->buttonList->remove('add');
        } else {
            if ($customerName) {
                $this->_headerText = __('All Reviews of Customer `%1`', $customerName);
            } elseif ($vendorName) {
                $this->_headerText = __('All Reviews of Vendor `%1`', $vendorName);
            } else {
                $this->_headerText = __('All Reviews');
            }
        }

        $this->buttonList->remove('add');
    }
}
