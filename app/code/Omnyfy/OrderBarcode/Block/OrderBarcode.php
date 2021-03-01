<?php
/**
 * Project: Order Barcode
 * User: jing
 * Date: 19/11/19
 * Time: 10:57 am
 */
namespace Omnyfy\OrderBarcode\Block;

use Magento\Framework\View\Element\Template;

class OrderBarcode extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Omnyfy\OrderBarcode\Helper\Data $helper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Sales\Model\Order|null
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    public function getBarcodeHtml()
    {
        $order = $this->getOrder();
        if (empty($order)) {
            return false;
        }

        if (empty($order->getId()) || empty($order->getIncrementId())) {
            return false;
        }

        return '<img src="data:image/png;base64,' . base64_encode($this->_helper->toBarcode($order->getIncrementId())) . '" />';

    }
}
 