<?php
namespace Omnyfy\Mcm\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class TotalFees extends Column
{
    protected $_orderRepository;
    protected $_searchCriteria;

    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;
    
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceFormatter;
    
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    /**
     * @var \Omnyfy\Vendor\Helper\Backend
     */
    protected $_backendHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $criteria
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Omnyfy\Vendor\Helper\Backend $backendHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
        PriceCurrencyInterface $priceFormatter,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Omnyfy\Vendor\Helper\Backend $backendHelper,
        array $components = [],
        array $data = []
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        $this->feesManagementResource = $feesManagementResource;
        $this->priceFormatter = $priceFormatter;
        $this->_adminSession = $adminSession;
        $this->_backendHelper = $backendHelper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $userData = $this->_adminSession->getUser()->getData();
        
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                $order = $this->_orderRepository->get($item["entity_id"]);
                $orderId = $order->getId();
                $seller_fee_total = $this->feesManagementResource->getTotalSellerFeeByOrderId($orderId);
                $category_commission_total = $this->feesManagementResource->getTotalCategoryFeeByOrderId($orderId);
                $disbursement_fee = $this->feesManagementResource->getDisbursementFeeByOrderId($orderId);
                $tax_on_fees = $this->feesManagementResource->getTaxOnFeesByOrderId($orderId);
                
                if ($this->_backendHelper->isVendor()) {
                    $userId = $userData['user_id'];
                    $vendorId = $this->feesManagementResource->getVendorByUserId($userId);
                    $seller_fee_total = $this->feesManagementResource->getVendorSellerFee($orderId, $vendorId);
                    $category_commission_total = $this->feesManagementResource->getVendorCategoryFee($orderId, $vendorId);
                    $disbursement_fee = $this->feesManagementResource->getVendorDisbursementFee($orderId, $vendorId);
                    $tax_on_fees = $this->feesManagementResource->getVendorTaxOnFees($orderId, $vendorId);
                }
                $total_fees = $seller_fee_total + $category_commission_total + $disbursement_fee + $tax_on_fees;

                $currencyCode = isset($item['base_currency_code']) ? $item['base_currency_code'] : null;
                $item[$this->getData('name')] = $this->priceFormatter->format(
                    $total_fees,
                    false,
                    null,
                    null,
                    $currencyCode
                );
            }
        }

        return $dataSource;
    }
}