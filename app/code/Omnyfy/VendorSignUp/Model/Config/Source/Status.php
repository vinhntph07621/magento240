<?php 
namespace Omnyfy\VendorSignUp\Model\Config\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve status options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('New')],
            ['value' => 1, 'label' => __('Approve')],
            ['value' => 2, 'label' => __('Reject')]
        ];
    }
}