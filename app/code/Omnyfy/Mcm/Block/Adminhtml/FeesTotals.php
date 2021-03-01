<?php

namespace Omnyfy\Mcm\Block\Adminhtml;

use Magento\Sales\Model\Order;
use Omnyfy\Mcm\Helper\Data as HelperData;
/**
 * Class FeesTotals
 * @package Omnyfy\Mcm\Block\Adminhtml
 */
class FeesTotals extends \Magento\Framework\View\Element\Template {

    /**
     * Associated array of totals
     * array(
     *  $totalCode => $totalObject
     * )
     *
     * @var array
     */
    protected $_totals;

    /**
     * @var Order|null
     */
    protected $_order = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    protected $_helper;

    /**
     * @var \Omnyfy\Vendor\Helper\Backend
     */
    protected $_backendHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Omnyfy\Mcm\Helper\Data $helper
     * @param \Omnyfy\Vendor\Helper\Backend $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
        \Magento\Backend\Model\Auth\Session $adminSession,
        HelperData $helper,
        \Omnyfy\Vendor\Helper\Backend $backendHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->feesManagementResource = $feesManagementResource;
        $this->_adminSession = $adminSession;
        $this->_helper = $helper;
        $this->_backendHelper = $backendHelper;
        parent::__construct($context, $data);
    }

    /**
     * Initialize self totals and children blocks totals before html building
     *
     * @return $this
     */
    protected function _beforeToHtml() {
        $this->_initTotals();
        foreach ($this->getLayout()->getChildBlocks($this->getNameInLayout()) as $child) {
            if (method_exists($child, 'initTotals') && is_callable([$child, 'initTotals'])) {
                $child->initTotals();
            }
        }
        return parent::_beforeToHtml();
    }

    /**
     * Get order object
     *
     * @return Order
     */
    public function getOrder() {
        if ($this->_order === null) {
            if ($this->hasData('order')) {
                $this->_order = $this->_getData('order');
            } elseif ($this->_coreRegistry->registry('current_order')) {
                $this->_order = $this->_coreRegistry->registry('current_order');
            } elseif ($this->getParentBlock()->getOrder()) {
                $this->_order = $this->getParentBlock()->getOrder();
            }
        }
        return $this->_order;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function setOrder($order) {
        $this->_order = $order;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return Order
     */
    public function getSource() {
        return $this->getOrder();
    }

    /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals() {

        $userData = $this->_adminSession->getUser()->getData();

        $source = $this->getSource();
        $orderId = $source->getId();

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
        
        $fees_total = $seller_fee_total + $category_commission_total + $disbursement_fee + $tax_on_fees;
        
        $this->_totals = [];
        $this->_totals['seller_fee_total'] = new \Magento\Framework\DataObject(
                [
            'code' => 'seller_fee_total',
            'value' => $seller_fee_total,
            //'base_value' => $seller_fee_total,
            'label' => __('Seller Fee Total')
                ]
        );

        $this->_totals['category_commission_total'] = new \Magento\Framework\DataObject(
                [
            'code' => 'category_commission_total',
            'value' => $category_commission_total,
            'label' => __('Category Commission Total')
                ]
        );

        $this->_totals['disbursement_fee'] = new \Magento\Framework\DataObject(
                [
            'code' => 'disbursement_fee',
            'value' => $disbursement_fee,
            'label' => __('Disbursment Fee')
                ]
        );

        $this->_totals['tax_on_fees'] = new \Magento\Framework\DataObject(
                [
            'code' => 'tax_on_fees',
            'value' => $tax_on_fees,
            'label' => __('Tax On Fees')
                ]
        );


        $this->_totals['fees_total'] = new \Magento\Framework\DataObject(
                [
            'code' => 'fees_total',
            'field' => 'fees_total',
            'strong' => true,
            'value' => $fees_total,
            'label' => __('Fees Total'),
                ]
        );

        /**
         * Base grandtotal
         */
        if ($this->getOrder()->isCurrencyDifferent()) {
            $this->_totals['base_grandtotal'] = new \Magento\Framework\DataObject(
                    [
                'code' => 'base_grandtotal',
                'value' => $this->getOrder()->formatBasePrice($source->getBaseGrandTotal()),
                'label' => __('Grand Total to be Charged'),
                'is_formated' => true,
                    ]
            );
        }
        return $this;
    }

    /**
     * Add new total to totals array after specific total or before last total by default
     *
     * @param   \Magento\Framework\DataObject $total
     * @param   null|string $after
     * @return  $this
     */
    public function addTotal(\Magento\Framework\DataObject $total, $after = null)
    {
        if ($after !== null && $after != 'last' && $after != 'first') {
            $totals = [];
            $added = false;
            foreach ($this->_totals as $code => $item) {
                $totals[$code] = $item;
                if ($code == $after) {
                    $added = true;
                    $totals[$total->getCode()] = $total;
                }
            }
            if (!$added) {
                $last = array_pop($totals);
                $totals[$total->getCode()] = $total;
                $totals[$last->getCode()] = $last;
            }
            $this->_totals = $totals;
        } elseif ($after == 'last') {
            $this->_totals[$total->getCode()] = $total;
        } elseif ($after == 'first') {
            $totals = [$total->getCode() => $total];
            $this->_totals = array_merge($totals, $this->_totals);
        } else {
            $last = array_pop($this->_totals);
            $this->_totals[$total->getCode()] = $total;
            $this->_totals[$last->getCode()] = $last;
        }
        return $this;
    }

    /**
     * Add new total to totals array before specific total or after first total by default
     *
     * @param   \Magento\Framework\DataObject $total
     * @param   null|string $before
     * @return  $this
     */
    public function addTotalBefore(\Magento\Framework\DataObject $total, $before = null) {
        if ($before !== null) {
            if (!is_array($before)) {
                $before = [$before];
            }
            foreach ($before as $beforeTotals) {
                if (isset($this->_totals[$beforeTotals])) {
                    $totals = [];
                    foreach ($this->_totals as $code => $item) {
                        if ($code == $beforeTotals) {
                            $totals[$total->getCode()] = $total;
                        }
                        $totals[$code] = $item;
                    }
                    $this->_totals = $totals;
                    return $this;
                }
            }
        }
        $totals = [];
        $first = array_shift($this->_totals);
        $totals[$first->getCode()] = $first;
        $totals[$total->getCode()] = $total;
        foreach ($this->_totals as $code => $item) {
            $totals[$code] = $item;
        }
        $this->_totals = $totals;
        return $this;
    }

    /**
     * Get Total object by code
     *
     * @param string $code
     * @return mixed
     */
    public function getTotal($code) {
        if (isset($this->_totals[$code])) {
            return $this->_totals[$code];
        }
        return false;
    }

    /**
     * Delete total by specific
     *
     * @param   string $code
     * @return  $this
     */
    public function removeTotal($code) {
        unset($this->_totals[$code]);
        return $this;
    }

    /**
     * Apply sort orders to totals array.
     * Array should have next structure
     * array(
     *  $totalCode => $totalSortOrder
     * )
     *
     *
     * @param   array $order
     * @return  $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function applySortOrder($order) {
        return $this;
    }

    /**
     * get totals array for visualization
     *
     * @param array|null $area
     * @return array
     */
    public function getTotals($area = null) {
        $totals = [];
        if ($area === null) {
            $totals = $this->_totals;
        } else {
            $area = (string) $area;
            foreach ($this->_totals as $total) {
                $totalArea = (string) $total->getArea();
                if ($totalArea == $area) {
                    $totals[] = $total;
                }
            }
        }
        return $totals;
    }

    /**
     * Format total value based on order currency
     *
     * @param   \Magento\Framework\DataObject $total
     * @return  string
     */
    public function formatValue($total) {
        if (!$total->getIsFormated()) {
            return $this->currency($total->getValue());
            //return $this->getOrder()->formatPrice($total->getValue());
        }
        return $total->getValue();
    }
    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }
    

}
