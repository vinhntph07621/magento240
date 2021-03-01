<?php
/**
 * Project: Multi Vendor M2.
 * User: seth
 * Date: 5/9/19
 * Time: 2:30 PM
 */

namespace Omnyfy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Omnyfy\Vendor\Model\Config;

class AddMarketplaceOwnerAbn implements ObserverInterface
{
    /**
     * @var \Omnyfy\Vendor\Model\Config
     */
    protected $_config;

    public function __construct(
        Config $config
    )
    {
        $this->_config = $config;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $transport = $observer->getData('transportObject');
        if ($this->_config->getInvoiceBy() == \Omnyfy\Vendor\Model\Config::INVOICE_BY_MO) {
            $transport->setData('marketplace_owner_abn_number', $this->_config->getMoAbn());
            $transport->setData('marketplace_owner_name', $this->_config->getMoName());
        }

    }
}