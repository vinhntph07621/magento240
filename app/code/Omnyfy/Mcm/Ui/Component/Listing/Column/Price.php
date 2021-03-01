<?php
namespace Omnyfy\Mcm\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Price extends Column
{
    protected $_orderRepository;
    protected $_searchCriteria;
    
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;
    
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
     * @param PriceCurrencyInterface $priceFormatter
     * @param UiComponentFactory $uiComponentFactory,
     * @param OrderRepositoryInterface $orderRepository,
     * @param SearchCriteriaBuilder $criteria,
     * @param \Magento\Backend\Model\Auth\Session $adminSession,
     * @param \Omnyfy\Vendor\Helper\Backend $backendHelper,
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
        array $data = [])
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        $this->priceFormatter = $priceFormatter;
        $this->feesManagementResource = $feesManagementResource;
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
                $currencyCode = isset($item['base_currency_code']) ? $item['base_currency_code'] : null;
                if ($this->_backendHelper->isVendor()) {
                    $userId = $userData['user_id'];
                    $vendorId = $this->feesManagementResource->getVendorByUserId($userId);
                    $vendorOrderTotals = $this->feesManagementResource->getVendorOrderTotals($vendorId, $orderId);
                    $grandTotal = $vendorOrderTotals['base_grand_total'] + $vendorOrderTotals['base_shipping_amount'] + $vendorOrderTotals['base_shipping_tax'] - $vendorOrderTotals['shipping_discount_amount'];
                    $item[$this->getData('name')] = $this->priceFormatter->format($grandTotal, false, null, null, $currencyCode);
                }else{
                 $item[$this->getData('name')] = $this->priceFormatter->format($order->getBaseGrandTotal(), false, null, null, $currencyCode);   
                }
            }
        }

        return $dataSource;
    }
}