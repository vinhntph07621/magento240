<?php


namespace Omnyfy\VendorAuth\Observer\Backend\Model;

use Magento\Sales\Model\Order\Interceptor;

class LoadBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected $_objectManager;
    protected $_registryManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

       

    }
}
