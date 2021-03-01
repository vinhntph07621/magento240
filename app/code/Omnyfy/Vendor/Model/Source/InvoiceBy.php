<?php
/**
 * Project: Vendors
 * User: jing
 * Date: 2019-03-04
 * Time: 11:07
 */
namespace Omnyfy\Vendor\Model\Source;

class InvoiceBy extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function toOptionArray()
    {
        return [
            [
                'value' => \Omnyfy\Vendor\Model\Config::INVOICE_BY_MO,
                'label' => __('Marketplace Owner')
            ],
            [
                'value' => \Omnyfy\Vendor\Model\Config::INVOICE_BY_VENDOR,
                'label' => __('Vendors')
            ]
        ];
    }

    public function toValuesArray()
    {
        return [
            \Omnyfy\Vendor\Model\Config::INVOICE_BY_MO => __('Marketplace Owner'),
            \Omnyfy\Vendor\Model\Config::INVOICE_BY_VENDOR => __('Vendors')
        ];
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
 