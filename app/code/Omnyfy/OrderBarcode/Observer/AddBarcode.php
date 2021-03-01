<?php
/**
 * Project: Order Barcode
 * User: jing
 * Date: 19/11/19
 * Time: 3:11 pm
 */
namespace Omnyfy\OrderBarcode\Observer;

class AddBarcode implements \Magento\Framework\Event\ObserverInterface
{
    protected $_helper;

    public function __construct(
        \Omnyfy\OrderBarcode\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getData('transportObject');
        $order = $transport->getOrder();
        if (empty($order) || empty($order->getIncrementId())) {
            return;
        }

        $barcode = $this->_helper->toBarcode($order->getIncrementId());

        //embaded image not working in google mail
        //$transport->setData('order_barcode', '<img src="data:image/png;base64,'. base64_encode($barcode) . '" />');

        $transport->setData('order_barcode', '<img src="cid:order_barcode.png" />');
        $attachments = $transport->getData('attachments');
        $attachments = is_array($attachments) ? $attachments : array();
        $attachments[] = array(
            'content' => $barcode,
            'type' => 'image/png',
            'name' => 'order_barcode.png',
            'disposition' => \Zend_Mime::DISPOSITION_INLINE
        );
        $transport->setData('attachments', $attachments);
    }
}
 